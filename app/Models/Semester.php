<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name',
        'number',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the academic year
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the current semester
     */
    public static function current()
    {
        return self::where('is_current', true)->first();
    }

    /**
     * Set this semester as current
     */
    public function setCurrent()
    {
        // Remove current flag from all semesters
        self::where('is_current', true)->update(['is_current' => false]);

        // Set this semester as current
        $this->is_current = true;
        $this->save();

        return $this;
    }
}
