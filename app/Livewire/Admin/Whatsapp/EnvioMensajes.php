<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\EducationalLevel;

class EnvioMensajes extends Component
{
    public $sendMode = 'individual';
    
    public $to = '';
    public $message = '';
    public $sending = false;
    public $success = null;
    public $error = null;
    public $jwtToken = null;
    public $recentMessages = [];
    public $charCount = 0;
    public $selectedTemplate = null;

    public $targetGroup = 'mayores';
    public $filterNivel = '';
    public $filterGrado = '';
    public $filterSeccion = '';
    public $selectedStudents = [];
    public $selectAll = false;
    public $students = [];
    public $sendProgress = 0;
    public $sendTotal = 0;
    public $sendResults = [];
    public $isSendingBulk = false;

    public $templates = [
        ['id' => 'saludo', 'name' => 'Saludo', 'message' => '¡Hola! Gracias por comunicarte con nosotros. ¿En qué podemos ayudarte?'],
        ['id' => 'confirmacion', 'name' => 'Confirmación', 'message' => 'Tu solicitud ha sido recibida correctamente. Te contactaremos pronto.'],
        ['id' => 'recordatorio', 'name' => 'Recordatorio', 'message' => 'Le recordamos que tiene una cita/actividad pendiente. Por favor confirme su asistencia.'],
        ['id' => 'pago', 'name' => 'Pago', 'message' => 'Le informamos que tiene un pago pendiente. Por favor comuníquese con administración para más detalles.'],
        ['id' => 'reunion', 'name' => 'Reunión', 'message' => 'Se le convoca a una reunión importante. Por favor confirme su asistencia.'],
    ];

    protected function rules()
    {
        if ($this->sendMode === 'individual') {
            return [
                'to' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
                'message' => 'required|string|min:1|max:1000'
            ];
        }
        return [
            'message' => 'required|string|min:1|max:1000',
            'selectedStudents' => 'required|array|min:1'
        ];
    }

    protected $messages = [
        'to.required' => 'El número de teléfono es obligatorio.',
        'to.min' => 'El número debe tener al menos 10 dígitos.',
        'to.max' => 'El número no puede tener más de 15 dígitos.',
        'to.regex' => 'El número solo puede contener dígitos.',
        'message.required' => 'El mensaje es obligatorio.',
        'message.min' => 'El mensaje no puede estar vacío.',
        'message.max' => 'El mensaje no puede exceder 1000 caracteres.',
        'selectedStudents.required' => 'Debe seleccionar al menos un destinatario.',
        'selectedStudents.min' => 'Debe seleccionar al menos un destinatario.'
    ];

    public function mount()
    {
        $this->generateToken();
        $this->loadRecentMessages();
    }

    public function generateToken()
    {
        $empresa = auth()->user()->empresa ?? null;
        $this->jwtToken = $empresa->api_key ?? null;
    }

    public function updatedMessage($value)
    {
        $this->charCount = strlen($value);
    }

    public function updatedSendMode()
    {
        $this->resetValidation();
        $this->clearMessages();
        if ($this->sendMode === 'grupal') {
            $this->loadStudents();
        }
    }

    public function updatedTargetGroup()
    {
        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->loadStudents();
    }

    public function updatedFilterNivel()
    {
        $this->filterGrado = '';
        $this->filterSeccion = '';
        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->loadStudents();
    }

    public function updatedFilterGrado()
    {
        $this->filterSeccion = '';
        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->loadStudents();
    }

    public function updatedFilterSeccion()
    {
        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->loadStudents();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = collect($this->students)->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function loadStudents()
    {
        $query = Student::query()
            ->where('status', true)
            ->whereNotNull('fecha_nacimiento');

        if ($this->targetGroup === 'mayores') {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) >= 18');
        } else {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18')
                  ->whereNotNull('representante_telefonos');
        }

        if ($this->filterNivel) {
            $query->where('nivel_educativo_id', $this->filterNivel);
        }
        if ($this->filterGrado) {
            $query->where('grado', $this->filterGrado);
        }
        if ($this->filterSeccion) {
            $query->where('seccion', $this->filterSeccion);
        }

        $this->students = $query->select([
            'id', 'nombres', 'apellidos', 'fecha_nacimiento', 'grado', 'seccion',
            'correo_electronico', 'representante_nombres', 'representante_apellidos',
            'representante_telefonos'
        ])
        ->orderBy('apellidos')
        ->limit(100)
        ->get()
        ->map(function ($student) {
            $phone = null;
            if ($this->targetGroup === 'menores' && $student->representante_telefonos) {
                $phones = is_array($student->representante_telefonos) 
                    ? $student->representante_telefonos 
                    : json_decode($student->representante_telefonos, true);
                $phone = $phones[0] ?? null;
            }
            
            return [
                'id' => $student->id,
                'nombre' => $student->nombres . ' ' . $student->apellidos,
                'edad' => $student->edad,
                'grado' => $student->grado . ' ' . $student->seccion,
                'representante' => $student->representante_nombres . ' ' . $student->representante_apellidos,
                'telefono' => $phone,
                'tiene_telefono' => !empty($phone)
            ];
        })
        ->toArray();
    }

    public function useTemplate($templateId)
    {
        $template = collect($this->templates)->firstWhere('id', $templateId);
        if ($template) {
            $this->message = $template['message'];
            $this->charCount = strlen($this->message);
            $this->selectedTemplate = $templateId;
        }
    }

    public function sendMessage()
    {
        if ($this->sendMode === 'grupal') {
            $this->sendBulkMessages();
            return;
        }

        $this->validate();

        if (!$this->jwtToken) {
            $this->error = 'No se ha configurado la API Key.';
            return;
        }

        $this->sending = true;
        $this->success = null;
        $this->error = null;

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-API-Key' => $this->jwtToken,
                    'Content-Type' => 'application/json'
                ])
                ->post(config('whatsapp.api_url') . '/api/whatsapp/send', [
                    'to' => $this->to,
                    'message' => $this->message
                ]);

            if ($response->successful()) {
                $this->success = 'Mensaje enviado exitosamente a ' . $this->to;
                $this->reset(['to', 'charCount', 'selectedTemplate']);
                $this->loadRecentMessages();
                $this->dispatch('refreshWhatsapp');
            } else {
                $errorData = $response->json();
                $this->error = $errorData['error'] ?? 'Error al enviar el mensaje.';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->error = 'No se puede conectar al servidor de WhatsApp.';
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->sending = false;
    }

    public function sendBulkMessages()
    {
        $this->validate([
            'message' => 'required|string|min:1|max:1000',
            'selectedStudents' => 'required|array|min:1'
        ]);

        if (!$this->jwtToken) {
            $this->error = 'No se ha configurado la API Key.';
            return;
        }

        $this->isSendingBulk = true;
        $this->sendProgress = 0;
        $this->sendResults = ['success' => 0, 'failed' => 0, 'skipped' => 0];
        $this->error = null;
        $this->success = null;

        $selectedData = collect($this->students)
            ->whereIn('id', $this->selectedStudents)
            ->filter(fn($s) => $s['tiene_telefono'])
            ->values();

        $this->sendTotal = $selectedData->count();
        $skipped = count($this->selectedStudents) - $this->sendTotal;
        $this->sendResults['skipped'] = $skipped;

        foreach ($selectedData as $index => $student) {
            try {
                $phone = preg_replace('/[^0-9]/', '', $student['telefono']);
                
                $personalizedMessage = str_replace(
                    ['{nombre}', '{estudiante}', '{grado}'],
                    [$student['representante'] ?: $student['nombre'], $student['nombre'], $student['grado']],
                    $this->message
                );

                $response = Http::timeout(10)
                    ->withHeaders([
                        'X-API-Key' => $this->jwtToken,
                        'Content-Type' => 'application/json'
                    ])
                    ->post(config('whatsapp.api_url') . '/api/whatsapp/send', [
                        'to' => $phone,
                        'message' => $personalizedMessage
                    ]);

                if ($response->successful()) {
                    $this->sendResults['success']++;
                } else {
                    $this->sendResults['failed']++;
                }
            } catch (\Exception $e) {
                $this->sendResults['failed']++;
            }

            $this->sendProgress = $index + 1;
            usleep(500000);
        }

        $this->isSendingBulk = false;
        
        if ($this->sendResults['success'] > 0) {
            $this->success = "Envío completado: {$this->sendResults['success']} enviados, {$this->sendResults['failed']} fallidos" . 
                ($this->sendResults['skipped'] > 0 ? ", {$this->sendResults['skipped']} sin teléfono" : "");
        } else {
            $this->error = "No se pudo enviar ningún mensaje. Verifique los números de teléfono.";
        }

        $this->selectedStudents = [];
        $this->selectAll = false;
        $this->loadRecentMessages();
        $this->dispatch('refreshWhatsapp');
    }

    public function loadRecentMessages()
    {
        if (!$this->jwtToken) return;

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $this->jwtToken])
                ->get(config('whatsapp.api_url') . '/api/whatsapp/messages?limit=5');

            if ($response->successful()) {
                $data = $response->json();
                $this->recentMessages = collect($data['messages'] ?? [])
                    ->where('status', 'sent')
                    ->take(5)
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
            // Silencioso
        }
    }

    public function clearMessages()
    {
        $this->reset(['success', 'error', 'sendResults']);
    }

    public function clearForm()
    {
        $this->reset(['to', 'message', 'charCount', 'selectedTemplate', 'success', 'error', 'selectedStudents', 'selectAll', 'sendResults']);
    }

    public function resend($to, $message)
    {
        $this->sendMode = 'individual';
        $this->to = $to;
        $this->message = $message;
        $this->charCount = strlen($message);
    }

    public function getNivelesProperty()
    {
        return EducationalLevel::orderBy('nombre')->get();
    }

    public function getGradosProperty()
    {
        $query = Student::query()->whereNotNull('grado');
        if ($this->filterNivel) {
            $query->where('nivel_educativo_id', $this->filterNivel);
        }
        return $query->distinct()->orderBy('grado')->pluck('grado');
    }

    public function getSeccionesProperty()
    {
        $query = Student::query()->whereNotNull('seccion');
        if ($this->filterNivel) {
            $query->where('nivel_educativo_id', $this->filterNivel);
        }
        if ($this->filterGrado) {
            $query->where('grado', $this->filterGrado);
        }
        return $query->distinct()->orderBy('seccion')->pluck('seccion');
    }

    public function getSelectedCountProperty()
    {
        return count($this->selectedStudents);
    }

    public function getSelectedWithPhoneCountProperty()
    {
        return collect($this->students)
            ->whereIn('id', $this->selectedStudents)
            ->filter(fn($s) => $s['tiene_telefono'])
            ->count();
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.envio-mensajes', [
            'niveles' => $this->niveles,
            'grados' => $this->grados,
            'secciones' => $this->secciones,
            'selectedCount' => $this->selectedCount,
            'selectedWithPhoneCount' => $this->selectedWithPhoneCount
        ]);
    }
}
