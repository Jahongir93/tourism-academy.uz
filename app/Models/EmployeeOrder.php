<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'order_type',
        'order_number',
        'order_date',
        'description',
        'file_path',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}