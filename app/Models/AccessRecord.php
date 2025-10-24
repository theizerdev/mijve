<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'date',
        'entry_time',
        'exit_time',
        'entry_user_id',
        'exit_user_id',
        'access_type',
        'access_method',
        'reference_code',
        'observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'entry_user_id' => 'integer',
        'exit_user_id' => 'integer',
    ];

    /**
     * Get the student that owns the access record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the entry user that owns the access record.
     */
    public function entryUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entry_user_id');
    }

    /**
     * Get the exit user that owns the access record.
     */
    public function exitUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exit_user_id');
    }

    /**
     * Scope a query to only include entry records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntry($query)
    {
        return $query->where('access_type', 'entry');
    }

    /**
     * Scope a query to only include exit records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExit($query)
    {
        return $query->where('access_type', 'exit');
    }

    /**
     * Scope a query to only include records from a specific date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to only include records for a specific student.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $studentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}

