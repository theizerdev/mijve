<?php

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Iniciando sincronización manual...\n";

    // Probar conexión a MySQL principal
    echo "🔌 Probando conexión a MySQL principal...\n";
    DB::connection('mysql')->getPdo();
    echo "✅ Conexión MySQL principal exitosa\n";

    // Probar conexión a WhatsApp DB
    echo "🔌 Probando conexión a WhatsApp DB (timeout 5s)...\n";
    try {
        DB::connection('whatsapp_api')->getPdo();
        echo "✅ Conexión WhatsApp DB exitosa\n";
    } catch (\Exception $e) {
        die("❌ Error conectando a WhatsApp DB: " . $e->getMessage() . "\n");
    }

    // 1. Obtener la empresa principal
    $empresa = Empresa::find(1);
    if (!$empresa) {
        die("❌ Empresa con ID 1 no encontrada.\n");
    }

    echo "✅ Empresa encontrada: " . $empresa->razon_social . "\n";
    echo "🔑 API Key actual en MIJVE: " . $empresa->whatsapp_api_key . "\n";

    // 2. Sincronizar con BD WhatsApp
    echo "📡 Sincronizando con BD WhatsApp...\n";

    try {
        $webhookUrl = config('whatsapp.api_url', 'http://158.69.175.224') . '/api/whatsapp/webhook';
        
        DB::connection('whatsapp_api')->table('companies')->updateOrInsert(
            ['id' => $empresa->id],
            [
                'name' => $empresa->razon_social ?? 'Empresa',
                'apiKey' => $empresa->whatsapp_api_key,
                'webhookUrl' => $webhookUrl,
                'rateLimitPerMinute' => 60,
                'isActive' => 1,
                'createdAt' => now(),
                'updatedAt' => now()
            ]
        );
        echo "✅ Sincronización completada.\n";
    } catch (\Exception $e) {
        die("❌ Error en sincronización: " . $e->getMessage() . "\n");
    }

    echo "🎉 Proceso finalizado.\n";

} catch (\Exception $e) {
    echo "❌ Error crítico: " . $e->getMessage() . "\n";
}
