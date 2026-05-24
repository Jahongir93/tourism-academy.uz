<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VedomostAssessmentColumn extends Model
{
    protected $fillable = [
        'vedomost_sheet_id',
        'name',
        'column_type',
        'max_score',
        'order',
        'is_final',
        'is_active',
    ];

    protected $casts = [
        'is_final' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the vedomost sheet that owns this column
     */
    public function vedomostSheet(): BelongsTo
    {
        return $this->belongsTo(VedomostSheet::class);
    }

    /**
     * Get grades for this column
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'assessment_column_id');
    }
}
