<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;
use App\Models\WhatsAppMessage;
use App\Models\Pais;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Extension;
use App\Models\Actividad;
use Livewire\Livewire;
use App\Livewire\RegistroParticipante;

class WhatsAppParticipantNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message_success_creates_sent_record(): void
    {
        Http::fake([
            '*' => Http::response(['success' => true, 'messageId' => 'abc123'], 200),
        ]);

        $service = new WhatsAppService(1);

        $result = $service->sendMessageWithLogAndRetry('5804121234567', 'Hola', [
            'context' => 'registro_participante',
            'participant_id' => 1,
            'recipient_name' => 'Juan Pérez',
        ]);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('whatsapp_messages', [
            'recipient_phone' => '5804121234567',
            'status' => 'sent',
        ]);
        $message = WhatsAppMessage::first();
        $this->assertNotNull($message->message_id);
        $this->assertSame(0, $message->retry_count);
    }

    public function test_send_message_retries_and_succeeds(): void
    {
        Http::fakeSequence()
            ->push([], 500)
            ->push(['success' => true, 'messageId' => 'ret123'], 200);

        $service = new WhatsAppService(1);
        $result = $service->sendMessageWithLogAndRetry('5804127654321', 'Hola', [
            'context' => 'registro_participante',
            'participant_id' => 2,
            'recipient_name' => 'Ana Ruiz',
        ]);

        $this->assertTrue($result['success']);
        $message = WhatsAppMessage::where('recipient_phone', '5804127654321')->first();
        $this->assertNotNull($message);
        $this->assertEquals('sent', $message->status);
        $this->assertGreaterThanOrEqual(1, $message->retry_count);
    }

    public function test_no_send_when_participant_has_no_phone(): void
    {
        Http::fake(); // Evitar llamadas reales

        $pais = Pais::create([
            'nombre' => 'Venezuela',
            'codigo_iso2' => 'VE',
            'codigo_iso3' => 'VEN',
            'codigo_telefonico' => '58',
            'moneda_principal' => 'USD',
            'idioma_principal' => 'es',
            'continente' => 'SA',
            'activo' => true,
        ]);

        $empresa = Empresa::create([
            'razon_social' => 'Centro Vargas',
            'documento' => 'J-12345678-9',
            'direccion' => 'Caracas',
            'status' => true,
            'telefono' => '02125551234',
            'email' => 'info@example.com',
            'pais_id' => $pais->id,
            'api_key' => 'test',
            'whatsapp_api_key' => 'test-api-key',
            'whatsapp_active' => true,
        ]);

        $lider = User::create([
            'name' => 'Líder Prueba',
            'email' => 'lider@example.com',
            'password' => bcrypt('password'),
            'telefono' => '04121234567',
            'empresa_id' => $empresa->id,
        ]);

        $extension = Extension::create([
            'empresa_id' => $empresa->id,
            'user_id' => $lider->id,
            'nombre' => 'Extensión Norte',
            'zona' => 'Zona 1',
            'distrito' => 'Distrito A',
            'status' => 'Activo',
        ]);

        $actividad = Actividad::create([
            'empresa_id' => $empresa->id,
            'nombre' => 'Campamento Juvenil',
            'capacidad' => 50,
            'cupos_ocupados' => 0,
            'status' => 'Activo',
            'edad_desde' => 12,
            'edad_hasta' => 20,
        ]);

        Livewire::test(RegistroParticipante::class)
            ->set('empresa_id', $empresa->id)
            ->set('extension_id', $extension->id)
            ->set('actividad_id', $actividad->id)
            ->set('acepta_terminos', true)
            ->set('nombres', 'Carlos')
            ->set('apellidos', 'Gómez')
            ->set('fecha_nacimiento', now()->subYears(15)->format('Y-m-d'))
            ->set('edad', 15)
            ->set('genero', 'Masculino')
            ->set('tipo_miembro', 'Miembro Activo')
            ->set('telefono_principal', '')
            ->set('telefono_alternativo', '')
            ->call('save');

        $this->assertDatabaseMissing('whatsapp_messages', [
            'metadata->context' => 'registro_participante',
        ]);
    }
}

