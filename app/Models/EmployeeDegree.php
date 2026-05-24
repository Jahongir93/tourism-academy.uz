<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDegree extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'degree_type',
        'specialty',
        'dissertation_title',
        'issued_date',
        'document_number',
        'notes'
    ];

    protected $casts = [
        'issued_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}