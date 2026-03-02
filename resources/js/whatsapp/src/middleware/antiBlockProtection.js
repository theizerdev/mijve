const logger = require('../utils/logger');
let redis;
try {
  redis = require('../config/redis');
} catch (error) {
  redis = null;
}

class AntiBlockProtection {
  constructor() {
    // Límites configurables
    this.MESSAGE_DELAY_MS = parseInt(process.env.ANTI_BLOCK_MESSAGE_DELAY_MS) || 5000; // 5 segundos
    this.MAX_MESSAGES_PER_HOUR_PER_USER = parseInt(process.env.ANTI_BLOCK_MAX_PER_HOUR) || 20;
    this.MAX_MESSAGES_PER_DAY_PER_USER = parseInt(process.env.ANTI_BLOCK_MAX_PER_DAY) || 100;
    this.BUSINESS_HOURS_START = parseInt(process.env.ANTI_BLOCK_BUSINESS_HOURS_START) || 7;
    this.BUSINESS_HOURS_END = parseInt(process.env.ANTI_BLOCK_BUSINESS_HOURS_END) || 22;
    this.DEFAULT_TIMEZONE = process.env.APP_TIMEZONE || 'America/Caracas';

    // Cache en memoria como fallback
    this.memoryCache = new Map();
  }

  /**
   * Obtiene la hora actual en la zona horaria configurada
   */
  getCurrentTime(timezone = this.DEFAULT_TIMEZONE) {
    try {
      return new Date(new Date().toLocaleString("en-US", { timeZone: timezone }));
    } catch (error) {
      logger.warn(`Timezone inválida (${timezone}), usando UTC`);
      return new Date();
    }
  }

  /**
   * Valida horarios comerciales con soporte de zona horaria
   */
  validateBusinessHours(timezone = this.DEFAULT_TIMEZONE) {
    // Se ha deshabilitado la restricción horaria a petición del usuario para permitir envío 24/7
    // Mantengo el método vacío para compatibilidad futura si se decide reactivar
    return true; 
    
    /* 
    const now = this.getCurrentTime(timezone);
    const hour = now.getHours();
    const day = now.getDay();
    
    // Fuera de horario comercial
    if (hour < this.BUSINESS_HOURS_START || hour >= this.BUSINESS_HOURS_END) {
      throw new Error(`Mensajes no permitidos fuera del horario comercial (${this.BUSINESS_HOURS_START}:00 - ${this.BUSINESS_HOURS_END}:00)`);
    }
    
    // Domingos (día 0)
    if (day === 0) {
      throw new Error('Mensajes no permitidos los domingos');
    }
    */
  }

  /**
   * Valida el contenido del mensaje contra spam
   */
  validateMessageContent(message) {
    if (!message || typeof message !== 'string') return;
    
    if (message.length > 4096) {
      throw new Error('Mensaje demasiado largo (máximo 4096 caracteres)');
    }
    
    // Detectar patrones de spam (Mejorado)
    const spamPatterns = [
      /(.)\1{10,}/, // Repetición excesiva de caracteres
      /\b(ganaste|premio|sorteo|millonario)\b/i, // Palabras clave de estafa
      /https?:\/\/(?!wa\.me|youtube\.com|google\.com|maps\.google\.com).{50,}/i // URLs largas sospechosas
    ];

    // Whitelist para referencias numéricas (Cédulas, Cuentas, Referencias)
    // Si el mensaje es corto y parece una referencia, lo permitimos
    const isReference = /^\d{8,20}$/.test(message.trim()) || 
                       /^Ref:?\s*\d+$/i.test(message.trim()) ||
                       /^CI:?\s*\d+$/i.test(message.trim());

    if (!isReference) {
        // Solo aplicar filtro de números largos si no parece una referencia explícita
        // Y si el mensaje contiene MUCHOS números en proporción al texto
        const digitCount = (message.match(/\d/g) || []).length;
        if (digitCount > 20 && message.length < 50) {
             throw new Error('Mensaje detectado como spam (exceso de dígitos)');
        }

        for (const pattern of spamPatterns) {
            if (pattern.test(message)) {
                throw new Error('Mensaje detectado como spam por contenido');
            }
        }
    }
  }

  /**
   * Verifica límites usando Redis (o Memoria)
   */
  async checkRateLimits(companyId, to) {
    const now = new Date();
    const hourKey = `antiblock:${companyId}:${to}:hour:${now.getHours()}`;
    const dayKey = `antiblock:${companyId}:${to}:day:${now.getDate()}`;
    
    let hourlyCount = 0;
    let dailyCount = 0;

    if (redis) {
      // Usar Redis (Atómico)
      hourlyCount = await redis.incr(hourKey);
      if (hourlyCount === 1) await redis.expire(hourKey, 3600); // 1 hora

      dailyCount = await redis.incr(dayKey);
      if (dailyCount === 1) await redis.expire(dayKey, 86400); // 24 horas
    } else {
      // Fallback Memoria
      hourlyCount = (this.memoryCache.get(hourKey) || 0) + 1;
      this.memoryCache.set(hourKey, hourlyCount);
      
      dailyCount = (this.memoryCache.get(dayKey) || 0) + 1;
      this.memoryCache.set(dayKey, dailyCount);
      
      // Limpieza simple de memoria
      if (this.memoryCache.size > 5000) this.memoryCache.clear();
    }

    if (hourlyCount > this.MAX_MESSAGES_PER_HOUR_PER_USER) {
      throw new Error(`Límite de ${this.MAX_MESSAGES_PER_HOUR_PER_USER} mensajes por hora excedido para este usuario`);
    }

    if (dailyCount > this.MAX_MESSAGES_PER_DAY_PER_USER) {
      throw new Error(`Límite de ${this.MAX_MESSAGES_PER_DAY_PER_USER} mensajes por día excedido para este usuario`);
    }
  }

  async checkCompanyDailyLimit(companyId, limit) {
    if (!limit || limit <= 0) return;
    
    const key = `antiblock:company:${companyId}:day:${new Date().getDate()}`;
    let count = 0;

    if (redis) {
      count = await redis.incr(key);
      if (count === 1) await redis.expire(key, 86400);
    } else {
      count = (this.memoryCache.get(key) || 0) + 1;
      this.memoryCache.set(key, count);
    }

    if (count > limit) {
      throw new Error(`Límite diario de la empresa (${limit}) excedido`);
    }
  }

  /**
   * Método principal
   */
  async protectMessage(companyId, to, message, companyDailyLimit = null, timezone = null) {
    try {
      this.validateBusinessHours(timezone || this.DEFAULT_TIMEZONE);
      this.validateMessageContent(message);
      
      // Validar destinatario básico
      if (!to || !/^\d+@.+$/.test(to)) throw new Error('Destinatario inválido');

      await this.checkRateLimits(companyId, to);
      
      if (companyDailyLimit) {
        await this.checkCompanyDailyLimit(companyId, companyDailyLimit);
      }

      return true;
    } catch (error) {
      logger.warn(`Anti-block Blocked: ${error.message}`, { companyId, to });
      throw error;
    }
  }
}

module.exports = new AntiBlockProtection();
