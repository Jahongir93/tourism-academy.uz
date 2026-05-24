<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialtySubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialty_id',
        'subject_id',
        'semester',
        'course_year',
        'is_required',
        'credits',
        'hours_total'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'semester' => 'integer',
        'course_year' => 'integer',
        'credits' => 'integer',
        'hours_total' => 'integer'
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
