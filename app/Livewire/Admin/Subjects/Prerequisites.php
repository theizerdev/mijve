<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Subject;
use App\Models\SubjectPrerequisite;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Prerequisites extends Component
{
    use WithPagination, HasDynamicLayout;

    public $subject;
    public $showModal = false;
    public $prerequisiteId;
    public $prerequisite_subject_id;
    public $type = 'mandatory';
    public $notes;
    public $is_active = true;
    public $showInactive = false;

    protected $rules = [
        'prerequisite_subject_id' => 'required|exists:subjects,id',
        'type' => 'required|in:mandatory,recommended',
        'notes' => 'nullable|string|max:500',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'prerequisite_subject_id.required' => 'La materia prerrequisito es obligatoria.',
        'prerequisite_subject_id.exists' => 'La materia seleccionada no existe.',
        'type.required' => 'El tipo de prerrequisito es obligatorio.',
        'type.in' => 'El tipo debe ser obligatorio o recomendado.',
    ];

    public function mount(Subject $subject)
    {
        $this->subject = $subject;
    }

    public function render()
    {
        $query = SubjectPrerequisite::with(['prerequisiteSubject'])
            ->where('subject_id', $this->subject->id);

        if (!$this->showInactive) {
            $query->where('is_active', true);
        }

        $prerequisites = $query->paginate(10);

        $availableSubjects = Subject::where('id', '!=', $this->subject->id)
            ->where('program_id', $this->subject->program_id)
            ->where('is_active', true)
            ->whereDoesntHave('prerequisiteOf', function ($query) {
                $query->where('subject_id', $this->subject->id);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.subjects.prerequisites', [
            'prerequisites' => $prerequisites,
            'availableSubjects' => $availableSubjects,
        ])->layout($this->getLayout());
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $prerequisite = SubjectPrerequisite::findOrFail($id);
        $this->prerequisiteId = $id;
        $this->prerequisite_subject_id = $prerequisite->prerequisite_subject_id;
        $this->type = $prerequisite->type;
        $this->notes = $prerequisite->notes;
        $this->is_active = $prerequisite->is_active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        // Validar que no se cree un ciclo
        if ($this->wouldCreateCycle($this->subject->id, $this->prerequisite_subject_id)) {
            $this->addError('prerequisite_subject_id', 'No se puede crear este prerrequisito porque crearía un ciclo infinito.');
            return;
        }

        DB::transaction(function () {
            if ($this->prerequisiteId) {
                // Actualizar
                $prerequisite = SubjectPrerequisite::findOrFail($this->prerequisiteId);
                $prerequisite->update([
                    'prerequisite_subject_id' => $this->prerequisite_subject_id,
                    'type' => $this->type,
                    'notes' => $this->notes,
                    'is_active' => $this->is_active,
                ]);
                session()->flash('message', 'Prerrequisito actualizado correctamente.');
            } else {
                // Crear
                SubjectPrerequisite::create([
                    'subject_id' => $this->subject->id,
                    'prerequisite_subject_id' => $this->prerequisite_subject_id,
                    'type' => $this->type,
                    'notes' => $this->notes,
                    'is_active' => $this->is_active,
                ]);
                session()->flash('message', 'Prerrequisito creado correctamente.');
            }
        });

        $this->resetInputFields();
        $this->showModal = false;
    }

    public function delete($id)
    {
        $prerequisite = SubjectPrerequisite::findOrFail($id);
        $prerequisite->delete();
        session()->flash('message', 'Prerrequisito eliminado correctamente.');
    }

    public function toggleStatus($id)
    {
        $prerequisite = SubjectPrerequisite::findOrFail($id);
        $prerequisite->update(['is_active' => !$prerequisite->is_active]);
        session()->flash('message', 'Estado del prerrequisito actualizado.');
    }

    private function resetInputFields()
    {
        $this->prerequisiteId = null;
        $this->prerequisite_subject_id = null;
        $this->type = 'mandatory';
        $this->notes = null;
        $this->is_active = true;
    }

    /**
     * Verificar si crear este prerrequisito crearía un ciclo
     */
    private function wouldCreateCycle($subjectId, $prerequisiteSubjectId)
    {
        // Verificar si la materia prerrequisito ya tiene como prerrequisito a la materia actual
        return SubjectPrerequisite::where('subject_id', $prerequisiteSubjectId)
            ->where('prerequisite_subject_id', $subjectId)
            ->where('is_active', true)
            ->exists();
    }

    public function getBreadcrumb()
    {
        return [
            ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
            ['name' => 'Materias', 'route' => route('admin.subjects.index')],
            ['name' => $this->subject->name, 'route' => route('admin.subjects.show', $this->subject->id)],
            ['name' => 'Prerrequisitos', 'route' => null],
        ];
    }

    protected function getPageTitle(): string
    {
        return 'Prerrequisitos: ' . $this->subject->name;
    }
}