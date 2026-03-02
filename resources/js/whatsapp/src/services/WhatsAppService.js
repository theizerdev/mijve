const { 
  default: makeWASocket, 
  DisconnectReason, 
  useMultiFileAuthState,
  fetchLatestBaileysVersion,
  makeCacheableSignalKeyStore,
  Browsers
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const logger = require('../utils/logger');
const QueueService = require('./QueueService');
const Message = require('../models/Message');
const Session = require('../models/Session');

class WhatsAppService {
  constructor(io, company) {
    this.io = io;
    this.company = company; // Empresa asociada
    this.sock = null;
    this.qrCode = null;
    this.isConnected = false;
    this.isConnecting = false;
    
    // Ruta de sesión específica por empresa
    const sessionName = company ? `company_${company.id}` : (process.env.WHATSAPP_SESSION_NAME || 'default');
    this.sessionPath = path.join('storage', 'sessions', sessionName);
    
    this.queueService = QueueService;
    this.reconnectAttempts = 0;
    this.maxReconnectAttempts = 5;
    this.connectionState = 'disconnected';
    this.lastSeen = null;
  }

  async initialize() {
    try {
      logger.whatsapp('Inicializando servicio WhatsApp...');
      
      // Crear directorio de sesión si no existe
      await this.ensureSessionDirectory();
      
      // Obtener versión más reciente de Baileys
      const { version, isLatest } = await fetchLatestBaileysVersion();
      logger.whatsapp(`Usando Baileys v${version.join('.')}, es la última: ${isLatest}`);
      

      
      // Conectar
      await this.connect();
      
    } catch (error) {
      logger.error('Error inicializando WhatsApp service:', error);
      throw error;
    }
  }

  async ensureSessionDirectory() {
    try {
      await fs.access(this.sessionPath);
    } catch {
      await fs.mkdir(this.sessionPath, { recursive: true });
      logger.whatsapp(`Directorio de sesión creado: ${this.sessionPath}`);
    }
  }

  async connect() {
    if (this.isConnecting) {
      logger.whatsapp('Ya hay una conexión en progreso...');
      return;
    }

    this.isConnecting = true;
    this.connectionState = 'connecting';
    
    try {
      logger.whatsapp('Iniciando conexión a WhatsApp...');
      
      // Configurar autenticación multi-archivo
      const { state, saveCreds } = await useMultiFileAuthState(this.sessionPath);
      
      // Crear socket
      this.sock = makeWASocket({
        version: (await fetchLatestBaileysVersion()).version,
        auth: {
          creds: state.creds,
          keys: makeCacheableSignalKeyStore(state.keys, {
            trace: () => {},
            debug: () => {},
            info: () => {},
            warn: () => {},
            error: () => {},
            child: () => ({
              trace: () => {},
              debug: () => {},
              info: () => {},
              warn: () => {},
              error: () => {},
              child: () => ({})
            })
          })
        },
        browser: Browsers.macOS('Desktop'),
        printQRInTerminal: false,
        generateHighQualityLinkPreview: true,
        syncFullHistory: false,
        markOnlineOnConnect: true,
        logger: {
          trace: () => {},
          debug: () => {},
          info: () => {},
          warn: () => {},
          error: () => {},
          child: () => ({
            trace: () => {},
            debug: () => {},
            info: () => {},
            warn: () => {},
            error: () => {},
            child: () => ({})
          })
        },
        getMessage: async (key) => {
          return { conversation: 'Mensaje no disponible' };
        }
      });

      // Configurar event handlers
      this.setupEventHandlers(saveCreds);
      
    } catch (error) {
      logger.error('Error conectando a WhatsApp:', error);
      this.isConnecting = false;
      this.connectionState = 'error';
      throw error;
    }
  }

  setupEventHandlers(saveCreds) {
    // Manejo de actualizaciones de conexión
    this.sock.ev.on('connection.update', async (update) => {
      const { connection, lastDisconnect, qr } = update;
      
      logger.whatsapp('Actualización de conexión:', { 
        connection, 
        lastDisconnect: lastDisconnect?.error?.output?.statusCode 
      });

      if (qr) {
        await this.handleQRCode(qr);
      }

      if (connection === 'close') {
        await this.handleDisconnection(lastDisconnect);
      } else if (connection === 'open') {
        await this.handleConnection();
      }
    });

    // Guardar credenciales cuando cambien
    this.sock.ev.on('creds.update', saveCreds);

    // Manejo de mensajes entrantes
    this.sock.ev.on('messages.upsert', async (messageUpdate) => {
      await this.handleIncomingMessages(messageUpdate);
    });

    // Manejo de actualizaciones de mensajes (entregado, leído, etc.)
    this.sock.ev.on('messages.update', async (messageUpdates) => {
      await this.handleMessageUpdates(messageUpdates);
    });

    // Manejo de presencia (en línea, escribiendo, etc.)
    this.sock.ev.on('presence.update', async (presenceUpdate) => {
      logger.whatsapp('Actualización de presencia:', presenceUpdate);
    });

    // Manejo de contactos
    this.sock.ev.on('contacts.upsert', async (contacts) => {
      logger.whatsapp(`Contactos actualizados: ${contacts.length}`);
    });

    // Manejo de chats
    this.sock.ev.on('chats.upsert', async (chats) => {
      logger.whatsapp(`Chats actualizados: ${chats.length}`);
    });
  }

  async handleQRCode(qr) {
    try {
      this.qrCode = await QRCode.toDataURL(qr);
      this.connectionState = 'qr_ready';
      
      logger.whatsapp('Código QR generado');
      
      // Emitir QR a clientes conectados
      this.io.emit('qr-code', {
        qr: this.qrCode,
        timestamp: new Date().toISOString()
      });
      
      // Mostrar QR en terminal para desarrollo
      if (process.env.NODE_ENV === 'development') {
        const QRTerminal = require('qrcode-terminal');
        QRTerminal.generate(qr, { small: true });
      }

      // Persistir estado en DB
      if (this.company) {
        await Session.upsert({
          id: `session_${this.company.id}`,
          companyId: this.company.id,
          status: 'qr_ready',
          qrCode: qr,
          lastSeen: new Date(),
          updatedAt: new Date()
        });
      }
      
    } catch (error) {
      logger.error('Error generando código QR:', error);
    }
  }

  async handleConnection() {
    this.isConnected = true;
    this.isConnecting = false;
    this.connectionState = 'connected';
    this.qrCode = null;
    this.reconnectAttempts = 0;
    this.lastSeen = new Date();
    
    logger.whatsapp('✅ Conectado a WhatsApp exitosamente');
    
    // Persistir conexión exitosa
    if (this.company) {
      await Session.upsert({
        id: `session_${this.company.id}`,
        companyId: this.company.id,
        status: 'connected',
        qrCode: null,
        phoneNumber: this.sock?.user?.id?.split(':')[0],
        deviceName: this.sock?.user?.name || 'WhatsApp Web',
        lastSeen: new Date(),
        sessionData: {
          user: this.sock?.user,
          platform: 'baileys'
        },
        updatedAt: new Date()
      });
    }

    // Emitir estado de conexión
    this.io.emit('connection-status', {
      status: 'connected',
      timestamp: new Date().toISOString(),
      user: this.sock.user
    });
  }

  async handleDisconnection(lastDisconnect) {
    this.isConnected = false;
    this.isConnecting = false;
    this.qrCode = null;
    
    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
    const reason = lastDisconnect?.error?.output?.statusCode;
    
    logger.whatsapp('Desconectado de WhatsApp:', { 
      reason, 
      shouldReconnect,
      reconnectAttempts: this.reconnectAttempts 
    });
    
    this.connectionState = shouldReconnect ? 'reconnecting' : 'disconnected';
    
    // Actualizar estado en DB
    if (this.company) {
      await Session.update({
        status: this.connectionState,
        lastSeen: new Date(),
        qrCode: null
      }, {
        where: { companyId: this.company.id }
      });
    }

    // Emitir estado de desconexión
    this.io.emit('connection-status', {
      status: this.connectionState,
      reason,
      timestamp: new Date().toISOString()
    });
    
    if (shouldReconnect && this.reconnectAttempts < this.maxReconnectAttempts) {
      this.reconnectAttempts++;
      const delay = Math.min(1000 * Math.pow(2, this.reconnectAttempts), 30000);
      
      logger.whatsapp(`Reintentando conexión en ${delay}ms (intento ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
      
      setTimeout(() => {
        this.connect();
      }, delay);
    } else {
      this.connectionState = 'disconnected';
      
      // Marcar como desconectado permanentemente en DB
      if (this.company) {
        await Session.update({
          status: 'disconnected',
          lastSeen: new Date()
        }, {
          where: { companyId: this.company.id }
        });
      }

      logger.whatsapp('Máximo de reintentos alcanzado o sesión cerrada');
    }
  }

  async handleIncomingMessages(messageUpdate) {
    const { messages, type } = messageUpdate;
    
    for (const message of messages) {
      if (message.key.fromMe) continue; // Ignorar mensajes propios
      
      logger.message('received', {
        from: message.key.remoteJid,
        messageType: Object.keys(message.message || {})[0],
        timestamp: message.messageTimestamp
      });
      
      // Procesar mensaje
      await this.processIncomingMessage(message);
      

    }
  }

  async handleMessageUpdates(messageUpdates) {
    for (const update of messageUpdates) {
      logger.message('updated', {
        messageId: update.key.id,
        status: update.update?.status,
        timestamp: new Date().toISOString()
      });
      
      // Actualizar estado en base de datos
      await Message.update(
        { status: update.update?.status || 'delivered' },
        { where: { messageId: update.key.id } }
      );
    }
  }

  async processIncomingMessage(message) {
    try {
      // Ignorar mensajes de estado y mensajes sin contenido
      if (message.key.remoteJid === 'status@broadcast' || !message.message) {
        return;
      }

      // Extraer información del mensaje
      const messageInfo = {
        id: message.key.id,
        from: message.key.remoteJid,
        timestamp: message.messageTimestamp,
        message: message.message,
        pushName: message.pushName
      };
      
      // Validar que el mensaje tenga contenido
      const messageContent = JSON.stringify(messageInfo.message);
      if (!messageContent || messageContent === 'null') {
        return;
      }
      
      // Guardar en base de datos (evitar errores por duplicado usando upsert)
      await Message.upsert({
        messageId: messageInfo.id,
        from: messageInfo.from,
        to: this.sock.user?.id || 'self',
        message: messageContent,
        type: 'text',
        status: 'delivered',
        companyId: this.company ? this.company.id : 1
      });
      
      // Emitir a clientes conectados
      this.io.emit('message-received', messageInfo);
      
    } catch (error) {
      logger.error('Error procesando mensaje entrante:', error);
    }
  }

  async sendMessage(to, content, options = {}) {
    if (!this.isConnected) {
      throw new Error('WhatsApp no está conectado');
    }

    try {
      // Formatear número
      const jid = this.formatPhoneNumber(to);
      
      // Preparar mensaje
      let messageContent;
      if (typeof content === 'string') {
        messageContent = { text: content };
      } else {
        messageContent = content;
      }
      
      // Envío con reintentos y backoff exponencial
      const maxRetries = parseInt(process.env.WHATSAPP_MAX_RETRIES) || 3;
      const baseDelay = parseInt(process.env.WHATSAPP_RETRY_DELAY) || 2000; // ms
      let attempt = 0;
      let lastError = null;
      let result = null;
      
      while (attempt <= maxRetries) {
        try {
          result = await this.sock.sendMessage(jid, messageContent, options);
          
          logger.message('sent', {
            to: jid,
            messageId: result.key.id,
            timestamp: new Date().toISOString()
          });
          
          // Persistir éxito
          await Message.create({
            messageId: result.key.id,
            from: this.sock.user?.id || 'self',
            to: jid,
            message: typeof messageContent === 'string' ? messageContent : JSON.stringify(messageContent),
            type: options.type || 'text',
            status: 'sent',
            companyId: options.companyId || (this.company ? this.company.id : 1)
          });
          
          return {
            success: true,
            messageId: result.key.id,
            timestamp: result.messageTimestamp
          };
        } catch (err) {
          lastError = err;
          attempt += 1;
          
          // Clasificar error (network/transient)
          const isTransient = this.isTransientError(err);
          
          logger.error('Error enviando mensaje (intento ' + attempt + ')', {
            error: err.message,
            to: jid,
            transient: isTransient
          });
          
          if (!isTransient || attempt > maxRetries) {
            break;
          }
          
          const delay = baseDelay * Math.pow(2, attempt - 1);
          await this.sleep(delay);
        }
      }
      
      // Registrar fallo final
      await Message.create({
        messageId: 'FAILED_' + Date.now().toString(),
        from: this.sock.user?.id || 'self',
        to: jid,
        message: typeof messageContent === 'string' ? messageContent : JSON.stringify(messageContent),
        type: options.type || 'text',
        status: 'failed',
        errorMessage: lastError?.message || 'Error desconocido',
        retryCount: Math.min(maxRetries, attempt),
        companyId: options.companyId || 1
      });
      
      throw lastError || new Error('Fallo desconocido enviando mensaje');
      
    } catch (error) {
      logger.error('Error enviando mensaje:', error);
      throw error;
    }
  }

  formatPhoneNumber(phone, opts = {}) {
    // Limpiar número
    let cleaned = phone.replace(/\D/g, '');
    const countryCode = (opts.countryCode || process.env.DEFAULT_COUNTRY_CODE || '58').replace(/\D/g, '');
    
    // Si inicia con '+' eliminarlo
    if (cleaned.startsWith('+')) cleaned = cleaned.slice(1);
    
    // Agregar código de país si no lo tiene
    if (!cleaned.startsWith(countryCode)) {
      // Si empieza con 0 (ej: 0412...), quitar el 0 y agregar código
      if (cleaned.startsWith('0')) {
        cleaned = countryCode + cleaned.slice(1);
      } else if (cleaned.length >= 10) {
        cleaned = countryCode + cleaned;
      }
    } else {
      // Si después del código viene un 0, quitarlo (caso +580412...)
      const withoutCode = cleaned.slice(countryCode.length);
      if (withoutCode.startsWith('0')) {
        cleaned = countryCode + withoutCode.slice(1);
      }
    }
    
    // Agregar sufijo de WhatsApp
    return cleaned + '@s.whatsapp.net';
  }
  
  isTransientError(err) {
    // Heurística simple: errores de red/transitorios
    const msg = (err?.message || '').toLowerCase();
    return (
      msg.includes('timeout') ||
      msg.includes('network') ||
      msg.includes('socket') ||
      msg.includes('econnreset') ||
      msg.includes('temporary') ||
      msg.includes('rate limit')
    );
  }
  
  sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
  }

  getStatus() {
    return {
      isConnected: this.isConnected,
      connectionState: this.connectionState,
      qrCode: this.qrCode,
      user: this.sock?.user || null,
      lastSeen: this.lastSeen,
      reconnectAttempts: this.reconnectAttempts
    };
  }

  getQRCode() {
    return this.qrCode;
  }

  async logout() {
    if (this.sock) {
      await this.sock.logout();
      logger.whatsapp('Sesión cerrada exitosamente');
    }
  }

  async shutdown() {
    logger.whatsapp('Cerrando servicio WhatsApp...');
    
    if (this.sock) {
      this.sock.end();
    }
    

    logger.whatsapp('Servicio WhatsApp cerrado');
  }
}

module.exports = WhatsAppService;
