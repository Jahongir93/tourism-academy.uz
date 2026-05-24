<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentFamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'relationship_type',
        'full_name',
        'workplace',
        'position',
        'phone',
        'is_guardian'
    ];

    protected $casts = [
        'is_guardian' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getRelationshipLabelAttribute(): string
    {
        return match($this->relationship_type) {
            'ota' => 'Ota',
            'ona' => 'Ona',
            'aka' => 'Aka',
            'uka' => 'Uka',
            'opa' => 'Opa',
            'singil' => 'Singil',
            'turmush_ortoq' => 'Turmush o\'rtoq',
            'farzand' => 'Farzand',
            'boshqa' => 'Boshqa',
            default => $this->relationship_type
        };
    }

    public function isParent(): bool
    {
        return in_array($this->relationship_type, ['ota', 'ona']);
    }

    public function isSibling(): bool
    {
        return in_array($this->relationship_type, ['aka', 'uka', 'opa', 'singil']);
    }

    public function isSpouse(): bool
    {
        return $this->relationship_type === 'turmush_ortoq';
    }

    public function isChild(): bool
    {
        return $this->relationship_type === 'farzand';
    }
}