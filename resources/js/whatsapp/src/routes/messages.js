const express = require('express');
const Message = require('../models/Message');
const router = express.Router();
const companyAuth = require('../middleware/companyAuth');
const { query } = require('express-validator');

// Asegurar todas las rutas de este router con autenticación de compañía
router.use(companyAuth);

router.get('/', 
  [
    query('page').optional().isInt({ min: 1 }).toInt(),
    query('limit').optional().isInt({ min: 1, max: 100 }).toInt(),
    query('status').optional().isIn(['pending', 'sent', 'delivered', 'read', 'failed', 'received']),
    query('from').optional().isString().trim(),
    query('to').optional().isString().trim()
  ],
  async (req, res) => {
  try {
    const { page = 1, limit = 50, status, from, to } = req.query;
    
    // Filtro obligatorio por compañía (Seguridad Multi-tenant)
    const where = { companyId: req.company.id };
    
    if (status) where.status = status;
    if (from) where.from = { [require('sequelize').Op.like]: `%${from}%` };
    if (to) where.to = { [require('sequelize').Op.like]: `%${to}%` };

    const messages = await Message.findAndCountAll({
      where,
      limit: parseInt(limit),
      offset: (parseInt(page) - 1) * parseInt(limit),
      order: [['createdAt', 'DESC']]
    });
    
    res.json({
      success: true,
      messages: messages.rows,
      total: messages.count,
      page: parseInt(page),
      totalPages: Math.ceil(messages.count / parseInt(limit))
    });
  } catch (error) {
    res.status(500).json({ success: false, error: error.message });
  }
});

module.exports = router;