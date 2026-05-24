<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

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
        return static::where('name', $name)->update(['is_current' => true]);
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