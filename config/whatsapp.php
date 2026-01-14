<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con la API de WhatsApp
    |
    */

    'api_url' => env('WHATSAPP_API_URL', 'http://localhost:3000'),
    'api_key' => env('WHATSAPP_API_KEY', 'test-api-key-vargas-centro'),
    'api_token' => env('WHATSAPP_API_TOKEN', ''),
    'jwt_secret' => env('JWT_SECRET', ''),
    'timeout' => env('WHATSAPP_TIMEOUT', 30),
    
    /*
    |--------------------------------------------------------------------------
    | Laravel Integration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con Laravel
    |
    */
    
    'laravel_url' => env('LARAVEL_URL', env('APP_URL', 'http://localhost:8000')),
    'verify_endpoint' => env('LARAVEL_VERIFY_ENDPOINT', '/api/verify-token'),
    'laravel_api_key' => env('LARAVEL_API_KEY', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Timeouts
    |--------------------------------------------------------------------------
    |
    | Timeouts para las diferentes operaciones
    |
    */
    
    'timeouts' => [
        'connection' => 1,
        'health_check' => 0.5,
        'status' => 1,
        'send_message' => 10,
        'quick_check' => 0.3,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configuración de caché para optimizar el rendimiento
    |
    */
    
    'cache' => [
        'status_ttl' => 10, // segundos
        'key_prefix' => 'whatsapp_status_',
        'connection_ttl' => 5, // segundos para test de conexión
    ],
];