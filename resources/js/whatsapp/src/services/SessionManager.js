const WhatsAppService = require('./WhatsAppService');
const Company = require('../models/Company');
const Session = require('../models/Session');
const logger = require('../utils/logger');

class SessionManager {
  constructor(io) {
    this.io = io;
    this.sessions = new Map(); // Map<companyId, WhatsAppService>
  }

  /**
   * Inicializa las sesiones para todas las empresas activas
   */
  async initialize() {
    try {
      const companies = await Company.findAll({ where: { isActive: true } });
      
      logger.info(`Inicializando sesiones para ${companies.length} empresas...`);
      
      for (const company of companies) {
        // Verificar si tenía una sesión activa previamente
        const session = await Session.findOne({ where: { companyId: company.id } });
        
        if (session && (session.status === 'connected' || session.status === 'qr_ready')) {
            logger.info(`Recuperando sesión persistente para empresa ${company.name}`);
            await this.createSession(company);
        } else {
            logger.info(`Empresa ${company.name} sin sesión activa previa, esperando conexión manual.`);
        }
      }
      
      logger.info('✅ Inicialización de sesiones completada');
    } catch (error) {
      logger.error('Error inicializando SessionManager:', error);
    }
  }

  /**
   * Crea o recupera una sesión para una empresa
   * @param {Company} company 
   * @returns {Promise<WhatsAppService>}
   */
  async createSession(company) {
    if (this.sessions.has(company.id)) {
      return this.sessions.get(company.id);
    }

    logger.info(`Creando sesión para empresa: ${company.name} (ID: ${company.id})`);

    const whatsappService = new WhatsAppService(this.io, company);
    
    try {
      await whatsappService.initialize();
      this.sessions.set(company.id, whatsappService);
      return whatsappService;
    } catch (error) {
      logger.error(`Error creando sesión para empresa ${company.id}:`, error);
      throw error;
    }
  }

  /**
   * Obtiene la sesión de una empresa
   * @param {number} companyId 
   * @returns {WhatsAppService|undefined}
   */
  getSession(companyId) {
    return this.sessions.get(companyId);
  }

  /**
   * Elimina/Desconecta una sesión
   * @param {number} companyId 
   */
  async removeSession(companyId) {
    if (this.sessions.has(companyId)) {
      const session = this.sessions.get(companyId);
      await session.logout(); // O disconnect
      this.sessions.delete(companyId);
      logger.info(`Sesión eliminada para empresa ID: ${companyId}`);
    }
  }
}

module.exports = SessionManager;
