const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const Session = sequelize.define('Session', {
  id: {
    type: DataTypes.STRING,
    primaryKey: true
  },
  status: {
    type: DataTypes.ENUM('disconnected', 'connecting', 'connected', 'qr_ready', 'reconnecting'),
    defaultValue: 'disconnected'
  },
  qrCode: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  lastSeen: {
    type: DataTypes.DATE,
    allowNull: true
  },
  phoneNumber: {
    type: DataTypes.STRING,
    allowNull: true
  },
  deviceName: {
    type: DataTypes.STRING,
    allowNull: true
  },
  sessionData: {
    type: DataTypes.JSON,
    allowNull: true,
    comment: 'Almacena metadatos de la sesión como versión, plataforma, etc.'
  },
  companyId: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'companies',
      key: 'id'
    }
  }
}, {
  tableName: 'whatsapp_sessions',
  timestamps: true,
  indexes: [
    {
      unique: true,
      fields: ['companyId']
    },
    {
      name: 'sessions_status_idx',
      fields: ['status']
    }
  ]
});

module.exports = Session;