<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;

class SyncWhatsAppCompany extends Command
{
    protected $signature = 'whatsapp:sync-company';
    protected $description = 'Sincroniza empresa con API de WhatsApp';

    public function handle()
    {
        // Obtener empresa de vargasCentro
        $empresa = DB::table('empresas')->where('id', 1)->first();
        
        if (!$empresa) {
            $this->error('Empresa con ID 1 no encontrada en tabla empresas');
            return;
        }

        // Generar JWT token
        $jwtSecret = config('whatsapp.jwt_secret');
        $payload = [
            'company_id' => $empresa->id,
            'company_name' => $empresa->nombre ?? $empresa->name ?? 'U.E Vargas II',
            'iat' => time(),
            'exp' => time() + (365 * 24 * 60 * 60)
        ];
        $token = JWT::encode($payload, $jwtSecret, 'HS256');

        // Crear base de datos si no existe
        DB::statement('CREATE DATABASE IF NOT EXISTS whatsapp_api_v2');
        
        // Configurar conexión a whatsapp_api_v2
        config(['database.connections.whatsapp_api' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'whatsapp_api_v2',
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);
        
        // Crear tabla companies si no existe
        DB::connection('whatsapp_api')->statement('
            CREATE TABLE IF NOT EXISTS companies (
                id INT PRIMARY KEY AUTO_INCREMENT,
                name VARCHAR(255),
                apiKey TEXT,
                webhookUrl VARCHAR(255),
                rateLimitPerMinute INT DEFAULT 60,
                isActive BOOLEAN DEFAULT 1,
                createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ');

        // Sincronizar con base de datos whatsapp_api_v2
        $webhookUrl = config('whatsapp.api_url') . '/api/whatsapp/webhook';
        
        DB::connection('whatsapp_api')->statement("
            INSERT INTO companies (id, name, apiKey, webhookUrl, rateLimitPerMinute, isActive, createdAt, updatedAt) 
            VALUES (?, ?, ?, ?, 60, 1, NOW(), NOW())
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name),
            apiKey = VALUES(apiKey),
            webhookUrl = VALUES(webhookUrl),
            updatedAt = NOW()
        ", [
            $empresa->id,
            $empresa->nombre ?? $empresa->name ?? 'U.E Vargas II',
            $token,
            $webhookUrl
        ]);

        $nombreEmpresa = $empresa->nombre ?? $empresa->name ?? 'U.E Vargas II';
        $this->info("✅ Empresa {$nombreEmpresa} sincronizada con API de WhatsApp");
        $this->info("🔑 JWT Token: {$token}");
    }
}