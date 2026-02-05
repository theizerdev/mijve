<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Services\WhatsAppApiIntegrationService;
use Illuminate\Support\Facades\Log;

class EmpresaObserver
{
    protected $whatsappService;

    public function __construct(WhatsAppApiIntegrationService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Se ejecuta cuando se crea una nueva empresa.
     * Genera automáticamente la API key de WhatsApp y registra en la API.
     */
    public function created(Empresa $empresa)
    {
        try {
            Log::info('EmpresaObserver: Creando empresa en WhatsApp API', [
                'empresa_id' => $empresa->id,
                'razon_social' => $empresa->razon_social
            ]);

            // Sincronizar con WhatsApp API cuando se crea una empresa
            $apiKey = $this->whatsappService->createCompany($empresa);

            if ($apiKey) {
                Log::info('EmpresaObserver: Empresa sincronizada con WhatsApp', [
                    'empresa_id' => $empresa->id,
                    'whatsapp_api_key' => $apiKey
                ]);
            }
        } catch (\Exception $e) {
            Log::error('EmpresaObserver: Error sincronizando empresa con WhatsApp', [
                'empresa_id' => $empresa->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Se ejecuta cuando se actualiza una empresa.
     * Sincroniza cambios relevantes con la API de WhatsApp.
     */
    public function updated(Empresa $empresa)
    {
        // Sincronizar cambios si es necesario (razon_social o configuración de WhatsApp)
        if ($empresa->isDirty(['razon_social', 'whatsapp_rate_limit', 'whatsapp_active'])) {
            try {
                $this->whatsappService->updateCompany($empresa);
            } catch (\Exception $e) {
                Log::error('EmpresaObserver: Error actualizando empresa en WhatsApp', [
                    'empresa_id' => $empresa->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Se ejecuta cuando se elimina una empresa.
     * Elimina la empresa de la API de WhatsApp.
     */
    public function deleted(Empresa $empresa)
    {
        try {
            $this->whatsappService->deleteCompany($empresa);
            
            Log::info('EmpresaObserver: Empresa eliminada de WhatsApp API', [
                'empresa_id' => $empresa->id
            ]);
        } catch (\Exception $e) {
            Log::error('EmpresaObserver: Error eliminando empresa de WhatsApp', [
                'empresa_id' => $empresa->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}