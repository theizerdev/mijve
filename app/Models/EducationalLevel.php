<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalLevel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
        'cuota_inicial',
        'numero_cuotas',
        'status',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'niveles_educativos';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'costo' => 'decimal:2',
        'cuota_inicial' => 'decimal:2',
        'numero_cuotas' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the students for the educational level.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'nivel_educativo_id');
    }
}
