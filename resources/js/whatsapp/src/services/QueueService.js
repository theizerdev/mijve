let redis;
try {
  redis = require('../config/redis');
} catch (error) {
  redis = null;
}
const logger = require('../utils/logger');

class QueueService {
  constructor() {
    this.queueKey = 'whatsapp:message_queue';
    this.processingKey = 'whatsapp:processing';
    this.isProcessing = false;
    this.isProcessingMemory = false;
    this.sessionManager = null;
    this.memoryQueue = [];
    // Configuración de Rate Limit (en ms)
    this.messageDelay = process.env.MESSAGE_DELAY || 2000; // 2 segundos entre mensajes por defecto
  }

  setSessionManager(manager) {
    this.sessionManager = manager;
  }

  async addMessage(messageData) {
    try {
      const message = {
        id: Date.now().toString(),
        ...messageData,
        timestamp: new Date().toISOString(),
        retries: 0
      };

      if (redis) {
        await redis.lPush(this.queueKey, JSON.stringify(message));
        logger.info(`Message queued (Redis): ${message.id}`);
        
        if (!this.isProcessing) {
          this.processQueue();
        }
      } else {
        // Fallback a memoria con Rate Limiting
        this.memoryQueue.push(message);
        logger.info(`Message queued (Memory): ${message.id}. Queue size: ${this.memoryQueue.length}`);
        
        if (!this.isProcessingMemory) {
          this.processMemoryQueue();
        }
      }
      
      return message.id;
    } catch (error) {
      logger.error('Error adding message to queue:', error);
      throw error;
    }
  }

  async processQueue() {
    if (this.isProcessing || !redis) return;
    
    this.isProcessing = true;
    logger.info('Starting Redis queue processing');

    try {
      while (true) {
        // Usar brPop para esperar mensajes de forma eficiente
        const messageStr = await redis.brPop(redis.commandOptions({ isolated: true }), this.queueKey, 5);
        if (!messageStr) break; // Timeout o conexión cerrada

        const message = JSON.parse(messageStr.element);
        await this.processMessage(message);
        
        // Rate limiting simple para Redis también
        await new Promise(resolve => setTimeout(resolve, this.messageDelay));
      }
    } catch (error) {
      logger.error('Redis Queue processing error:', error);
    } finally {
      this.isProcessing = false;
      logger.info('Redis Queue processing stopped');
    }
  }

  async processMemoryQueue() {
    if (this.isProcessingMemory) return;

    this.isProcessingMemory = true;
    logger.info('Starting Memory queue processing');

    try {
      while (this.memoryQueue.length > 0) {
        const message = this.memoryQueue.shift();
        
        await this.processMessage(message);
        
        // Esperar antes del siguiente mensaje para evitar bloqueos
        if (this.memoryQueue.length > 0) {
          await new Promise(resolve => setTimeout(resolve, this.messageDelay));
        }
      }
    } catch (error) {
      logger.error('Memory Queue processing error:', error);
    } finally {
      this.isProcessingMemory = false;
      logger.info('Memory Queue processing stopped (Empty)');
    }
  }

  async processMessage(message) {
    try {
      if (!this.sessionManager) {
        throw new Error('SessionManager instance not set in QueueService');
      }

      const whatsappService = this.sessionManager.getSession(message.companyId);

      if (!whatsappService) {
        throw new Error(`No active session found for company ${message.companyId}`);
      }

      logger.debug(`Processing message: ${message.id} to ${message.to} (Company: ${message.companyId})`);

      await whatsappService.sendMessage(message.to, message.content, {
        type: message.type || 'text',
        companyId: message.companyId,
        mediaUrl: message.mediaUrl,
        caption: message.caption
      });
      
      logger.info(`Message processed successfully: ${message.id}`);
    } catch (error) {
      logger.error(`Failed to process message ${message.id}:`, error);
      
      // Lógica de reintento
      if (message.retries < 3) {
        message.retries++;
        logger.warn(`Re-queueing message ${message.id} (Attempt ${message.retries}/3)`);
        
        if (redis) {
          await redis.lPush(this.queueKey, JSON.stringify(message));
        } else {
          // En memoria, lo ponemos al final para no bloquear
          this.memoryQueue.push(message);
        }
      } else {
        logger.error(`Message failed permanently after 3 attempts: ${message.id}`);
        // Aquí se podría guardar en una tabla de "mensajes fallidos" en DB
      }
    }
  }

  async getQueueSize() {
    if (redis) return await redis.lLen(this.queueKey);
    return this.memoryQueue.length;
  }
}

module.exports = new QueueService();
