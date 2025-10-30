<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Multitenantable;

class EducationalLevel extends Model
{
    use HasFactory, SoftDeletes, Multitenantable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'empresa_id',
        'sucursal_id',
        'nombre',
        'descripcion',
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
        'status' => 'boolean',
    ];

    /**
     * Get the students for the educational level.
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'nivel_educativo_id');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }
}