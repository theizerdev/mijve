<?php

use App\Models\Empresa;
use Illuminate\Support\Facades\Http;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Iniciando sincronización vía HTTP API...\n";

    $empresa = Empresa::find(1);
    if (!$empresa) {
        die("❌ Empresa con ID 1 no encontrada.\n");
    }

    echo "✅ Empresa encontrada: " . $empresa->razon_social . "\n";
    echo "🔑 API Key: " . $empresa->whatsapp_api_key . "\n";

    $apiUrl = 'http://158.69.175.224:3001/api/companies/sync';
    $webhookUrl = 'http://158.69.175.224/api/whatsapp/webhook';

    echo "📡 Enviando datos a: $apiUrl\n";

    $response = Http::timeout(10)->post($apiUrl, [
        'id' => $empresa->id,
        'name' => $empresa->razon_social,
        'apiKey' => $empresa->whatsapp_api_key,
        'webhookUrl' => $webhookUrl,
        'rateLimitPerMinute' => 60,
        'isActive' => true
    ]);

    if ($response->successful()) {
        echo "✅ Sincronización exitosa\n";
        echo "📄 Respuesta: " . $response->body() . "\n";
    } else {
        echo "❌ Error HTTP " . $response->status() . "\n";
        echo "📄 Respuesta: " . $response->body() . "\n";
    }

    echo "🎉 Proceso finalizado.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
