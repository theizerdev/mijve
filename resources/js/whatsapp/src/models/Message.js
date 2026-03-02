const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/database');

const Message = sequelize.define('Message', {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true
  },
  messageId: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true
  },
  from: {
    type: DataTypes.STRING,
    allowNull: false
  },
  to: {
    type: DataTypes.STRING,
    allowNull: false
  },
  message: {
    type: DataTypes.TEXT,
    allowNull: false
  },
  type: {
    type: DataTypes.ENUM('text', 'image', 'document', 'audio', 'video'),
    defaultValue: 'text'
  },
  status: {
    type: DataTypes.ENUM('pending', 'sent', 'delivered', 'read', 'failed', 'received'),
    defaultValue: 'pending'
  },
  mediaUrl: {
    type: DataTypes.STRING,
    allowNull: true
  },
  retryCount: {
    type: DataTypes.INTEGER,
    defaultValue: 0
  },
  errorMessage: {
    type: DataTypes.TEXT,
    allowNull: true
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
  tableName: 'whatsapp_messages',
  timestamps: true,
  indexes: [
    {
      name: 'messages_company_created_idx',
      fields: ['companyId', 'createdAt']
    },
    {
      name: 'messages_company_status_idx',
      fields: ['companyId', 'status']
    },
    {
      name: 'messages_message_id_idx',
      unique: true,
      fields: ['messageId']
    }
  ]
});

module.exports = Message;
