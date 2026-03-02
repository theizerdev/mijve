<?php
/**
 * Script para ejecutar EN EL SERVIDOR 158.69.175.224
 * Este script debe copiarse al servidor y ejecutarse allí
 */

try {
    echo "Conectando a MySQL local...\n";
    
    $pdo = new PDO(
        'mysql:host=158.69.175.224;dbname=larawhatsapp;charset=utf8mb4',
        'root',
        'AdaThei04112023*',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "✅ Conexión exitosa\n";
    
    // Datos de la empresa
    $empresaId = 1;
    $nombre = 'Movimiento Misionero Mundial';
    $apiKey = 'vg_e43e36dcd7ca47992511f13c3c5f2b7db000312e62418fa4';
    $webhookUrl = 'http://158.69.175.224/api/whatsapp/webhook';
    
    // Verificar si existe
    $stmt = $pdo->prepare("SELECT id FROM companies WHERE id = ?");
    $stmt->execute([$empresaId]);
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "📝 Actualizando empresa existente...\n";
        $sql = "UPDATE companies SET 
                name = ?,
                apiKey = ?,
                webhookUrl = ?,
                rateLimitPerMinute = ?,
                isActive = ?,
                updatedAt = NOW()
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre, $apiKey, $webhookUrl, 60, 1, $empresaId]);
    } else {
        echo "➕ Insertando nueva empresa...\n";
        $sql = "INSERT INTO companies (id, name, apiKey, webhookUrl, rateLimitPerMinute, isActive, createdAt, updatedAt) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$empresaId, $nombre, $apiKey, $webhookUrl, 60, 1]);
    }
    
    echo "✅ Sincronización completada\n";
    
    // Verificar
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$empresaId]);
    $company = $stmt->fetch();
    
    echo "\n📊 Datos guardados:\n";
    echo "ID: " . $company['id'] . "\n";
    echo "Nombre: " . $company['name'] . "\n";
    echo "API Key: " . $company['apiKey'] . "\n";
    echo "Webhook: " . $company['webhookUrl'] . "\n";
    echo "Activo: " . ($company['isActive'] ? 'Sí' : 'No') . "\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
