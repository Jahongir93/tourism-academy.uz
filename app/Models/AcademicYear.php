<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    // NOTE: the real column is `year`. `name` is exposed as an alias via the
    // accessor/mutator below so existing code using ->name keeps working.
    protected $fillable = [
        'year',
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // `name` <-> `year` alias (table has no `name` column)
    public function getNameAttribute(): ?string
    {
        return $this->attributes['year'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['year'] = $value;
    }

    public static function current()
    {
        return static::where('is_current', true)->first();
    }
    
    public static function getCurrentYear()
    {
        $current = static::current();
        if ($current) {
            return $current->name;
        }

        // Generate default year if not found
        $year = date('Y');
        $month = date('n');

        if ($month >= 9) {
            return $year . '-' . ($year + 1);
        } else {
            return ($year - 1) . '-' . $year;
        }
    }

    public static function setCurrent($name)
    {
        static::where('is_current', true)->update(['is_current' => false]);
        return static::where('year', $name)->update(['is_current' => true]);
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }
}