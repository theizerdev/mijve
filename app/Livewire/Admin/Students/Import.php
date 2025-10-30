<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use App\Models\EducationalLevel;
use App\Models\Turno;
use App\Models\SchoolPeriod;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Import extends Component
{
    use WithFileUploads;

    public $file;
    public $preview = [];
    public $importing = false;
    public $imported = false;
    public $totalRows = 0;
    public $importedRows = 0;
    public $failedRows = 0;
    public $errorsList = [];
    
    // Campos para mapeo de columnas
    public $columnMapping = [
        'nombres' => '',
        'apellidos' => '',
        'fecha_nacimiento' => '',
        'documento_identidad' => '',
        'grado' => '',
        'seccion' => '',
        'nivel_educativo' => '',
        'turno' => '',
        'school_period' => '',
        'correo_electronico' => '',
        'representante_nombres' => '',
        'representante_apellidos' => '',
        'representante_documento_identidad' => '',
        'representante_telefonos' => '',
        'representante_correo' => '',
    ];
    
    protected $rules = [
        'file' => 'required|mimes:csv,txt,xlsx,xls|max:2048',
    ];

    public function render()
    {
        return view('livewire.admin.students.import')
            ->layout('components.layouts.admin', [
                'title' => 'Importar Estudiantes',
                'description' => 'Importar estudiantes masivamente desde un archivo CSV o Excel'
            ]);
    }

    public function updatedFile()
    {
        $this->validate();
        
        // Limpiar datos previos
        $this->preview = [];
        $this->errorsList = [];
        
        try {
            // Procesar archivo para previsualización
            $this->processFileForPreview();
        } catch (\Exception $e) {
            Log::error('Error al procesar archivo para previsualización: ' . $e->getMessage());
            session()->flash('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    private function processFileForPreview()
    {
        $path = $this->file->getRealPath();
        
        // Detectar tipo de archivo
        $extension = $this->file->getClientOriginalExtension();
        
        if (in_array($extension, ['xlsx', 'xls'])) {
            // Procesar archivo Excel
            $this->processExcelFile($path);
        } else {
            // Procesar archivo CSV
            $this->processCsvFile($path);
        }
    }

    private function processExcelFile($path)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Obtener encabezados
        $headers = [];
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headers[] = $worksheet->getCellByColumnAndRow($col, 1)->getValue();
        }
        
        // Obtener primeras 5 filas para previsualización
        $rows = [];
        $rowCount = min(5, $worksheet->getHighestRow() - 1);
        
        for ($row = 2; $row <= $rowCount + 1; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            $rows[] = $rowData;
        }
        
        $this->preview = [
            'headers' => $headers,
            'rows' => $rows
        ];
        
        $this->totalRows = $worksheet->getHighestRow() - 1;
    }

    private function processCsvFile($path)
    {
        $file = fopen($path, 'r');
        
        // Obtener encabezados
        $headers = fgetcsv($file, 1000, ',');
        
        // Obtener primeras 5 filas para previsualización
        $rows = [];
        $rowCount = 0;
        
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE && $rowCount < 5) {
            $rows[] = $data;
            $rowCount++;
        }
        
        fclose($file);
        
        $this->preview = [
            'headers' => $headers,
            'rows' => $rows
        ];
        
        // Contar total de filas
        $file = fopen($path, 'r');
        fgetcsv($file, 1000, ','); // Saltar encabezados
        $this->totalRows = 0;
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            $this->totalRows++;
        }
        fclose($file);
    }

    public function import()
    {
        $this->validate();
        
        if (empty(array_filter($this->columnMapping))) {
            session()->flash('error', 'Debe mapear al menos una columna.');
            return;
        }
        
        $this->importing = true;
        $this->imported = false;
        $this->importedRows = 0;
        $this->failedRows = 0;
        $this->errorsList = [];
        
        try {
            DB::beginTransaction();
            
            $path = $this->file->getRealPath();
            $extension = $this->file->getClientOriginalExtension();
            
            if (in_array($extension, ['xlsx', 'xls'])) {
                $this->importFromExcel($path);
            } else {
                $this->importFromCsv($path);
            }
            
            DB::commit();
            
            $this->importing = false;
            $this->imported = true;
            
            session()->flash('message', "Importación completada. {$this->importedRows} estudiantes importados, {$this->failedRows} errores.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al importar estudiantes: ' . $e->getMessage());
            $this->importing = false;
            session()->flash('error', 'Error al importar estudiantes: ' . $e->getMessage());
        }
    }

    private function importFromExcel($path)
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $spreadsheet = $reader->load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        
        // Procesar cada fila
        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $rowData[] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            
            $this->processRow($rowData, $row);
        }
    }

    private function importFromCsv($path)
    {
        $file = fopen($path, 'r');
        fgetcsv($file, 1000, ','); // Saltar encabezados
        
        $rowNumber = 2;
        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            $this->processRow($data, $rowNumber);
            $rowNumber++;
        }
        
        fclose($file);
    }

    private function processRow($data, $rowNumber)
    {
        try {
            // Mapear datos según la configuración
            $studentData = $this->mapRowData($data);
            
            // Validar datos requeridos
            if (empty($studentData['nombres']) || empty($studentData['apellidos'])) {
                $this->errorsList[] = "Fila {$rowNumber}: Nombres o apellidos vacíos";
                $this->failedRows++;
                return;
            }
            
            // Generar código único
            $studentData['codigo'] = $this->generateUniqueCode();
            
            // Asignar empresa y sucursal del usuario actual
            $studentData['empresa_id'] = auth()->user()->empresa_id;
            $studentData['sucursal_id'] = auth()->user()->sucursal_id;
            
            // Estado por defecto
            $studentData['status'] = true;
            
            // Crear estudiante
            Student::create($studentData);
            
            $this->importedRows++;
            
        } catch (\Exception $e) {
            Log::error("Error al procesar fila {$rowNumber}: " . $e->getMessage());
            $this->errorsList[] = "Fila {$rowNumber}: " . $e->getMessage();
            $this->failedRows++;
        }
    }

    private function mapRowData($data)
    {
        $mappedData = [];
        
        foreach ($this->columnMapping as $field => $columnIndex) {
            if ($columnIndex !== '' && isset($data[$columnIndex])) {
                $value = $data[$columnIndex];
                
                // Procesamiento especial para ciertos campos
                switch ($field) {
                    case 'fecha_nacimiento':
                        if ($value) {
                            try {
                                $mappedData[$field] = Carbon::parse($value)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $mappedData[$field] = null;
                            }
                        }
                        break;
                        
                    case 'representante_telefonos':
                        if ($value) {
                            // Convertir a array si es una lista separada por comas
                            $mappedData[$field] = explode(',', $value);
                        }
                        break;
                        
                    case 'nivel_educativo':
                        if ($value) {
                            // Buscar nivel educativo por nombre
                            $nivel = EducationalLevel::where('nombre', 'like', "%{$value}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($nivel) {
                                $mappedData['nivel_educativo_id'] = $nivel->id;
                            }
                        }
                        break;
                        
                    case 'turno':
                        if ($value) {
                            // Buscar turno por nombre
                            $turno = Turno::where('nombre', 'like', "%{$value}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($turno) {
                                $mappedData['turno_id'] = $turno->id;
                            }
                        }
                        break;
                        
                    case 'school_period':
                        if ($value) {
                            // Buscar período escolar por nombre
                            $period = SchoolPeriod::where('nombre', 'like', "%{$value}%")
                                ->where('empresa_id', auth()->user()->empresa_id)
                                ->first();
                            if ($period) {
                                $mappedData['school_periods_id'] = $period->id;
                            }
                        }
                        break;
                        
                    default:
                        $mappedData[$field] = $value;
                        break;
                }
            }
        }
        
        return $mappedData;
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'STU' . strtoupper(Str::random(6));
        } while (Student::where('codigo', $code)->exists());
        
        return $code;
    }

    public function resetImport()
    {
        $this->reset(['file', 'preview', 'importing', 'imported', 'totalRows', 'importedRows', 'failedRows', 'errorsList']);
        $this->columnMapping = [
            'nombres' => '',
            'apellidos' => '',
            'fecha_nacimiento' => '',
            'documento_identidad' => '',
            'grado' => '',
            'seccion' => '',
            'nivel_educativo' => '',
            'turno' => '',
            'school_period' => '',
            'correo_electronico' => '',
            'representante_nombres' => '',
            'representante_apellidos' => '',
            'representante_documento_identidad' => '',
            'representante_telefonos' => '',
            'representante_correo' => '',
        ];
    }
}