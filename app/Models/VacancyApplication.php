<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class VacancyApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vacancy_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'birth_date',
        'gender',
        'region',
        'city',
        'address',
        'education_level',
        'education_institution',
        'education_specialty',
        'graduation_year',
        'experience_years',
        'work_experience',
        'skills',
        'languages',
        'cover_letter',
        'resume_path',
        'photo_path',
        'additional_data',
        'status',
        'internal_notes',
        'reviewed_by',
        'reviewed_at',
        'response_message',
        'response_sent_at',
        'response_sent_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'graduation_year' => 'integer',
        'experience_years' => 'integer',
        'additional_data' => 'array',
        'reviewed_at' => 'datetime',
        'response_sent_at' => 'datetime',
    ];

    // Status labels
    const STATUSES = [
        'new' => ['label' => 'Yangi', 'color' => 'info', 'icon' => 'fa-envelope'],
        'reviewed' => ['label' => 'Ko\'rib chiqilgan', 'color' => 'primary', 'icon' => 'fa-eye'],
        'shortlisted' => ['label' => 'Tanlangan', 'color' => 'warning', 'icon' => 'fa-star'],
        'interview' => ['label' => 'Suhbatga chaqirilgan', 'color' => 'purple', 'icon' => 'fa-comments'],
        'offered' => ['label' => 'Taklif yuborilgan', 'color' => 'success', 'icon' => 'fa-handshake'],
        'hired' => ['label' => 'Ishga qabul qilingan', 'color' => 'success', 'icon' => 'fa-check-circle'],
        'rejected' => ['label' => 'Rad etilgan', 'color' => 'danger', 'icon' => 'fa-times-circle'],
    ];

    // Education levels
    const EDUCATION_LEVELS = [
        'secondary' => 'O\'rta',
        'vocational' => 'O\'rta maxsus',
        'incomplete_higher' => 'Tugallanmagan oliy',
        'bachelor' => 'Bakalavr',
        'master' => 'Magistr',
        'phd' => 'PhD / Fan nomzodi',
        'doctorate' => 'Fan doktori',
    ];

    // Relationships
    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function responseSender()
    {
        return $this->belongsTo(User::class, 'response_sent_by');
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->last_name, $this->first_name, $this->middle_name]);
        return implode(' ', $parts);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }

    public function getStatusIconAttribute()
    {
        return self::STATUSES[$this->status]['icon'] ?? 'fa-question';
    }

    public function getEducationLevelLabelAttribute()
    {
        return self::EDUCATION_LEVELS[$this->education_level] ?? $this->education_level;
    }

    public function getResumeUrlAttribute()
    {
        if ($this->resume_path) {
            return Storage::url($this->resume_path);
        }
        return null;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return Storage::url($this->photo_path);
        }
        return null;
    }

    public function getAgeAttribute()
    {
        if ($this->birth_date) {
            return $this->birth_date->age;
        }
        return null;
    }

    // Methods
    public function markAsReviewed($userId = null)
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_by' => $userId ?? auth()->id(),
            'reviewed_at' => now(),
        ]);
    }

    public function updateStatus($status, $userId = null)
    {
        $data = ['status' => $status];

        if ($this->status === 'new' && $status !== 'new') {
            $data['reviewed_by'] = $userId ?? auth()->id();
            $data['reviewed_at'] = now();
        }

        $this->update($data);
    }

    public function sendResponse($message, $userId = null)
    {
        $this->update([
            'response_message' => $message,
            'response_sent_at' => now(),
            'response_sent_by' => $userId ?? auth()->id(),
        ]);

        // Send email notification
        // TODO: Implement email sending
    }
}
