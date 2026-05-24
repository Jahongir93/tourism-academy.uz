<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_type',
        'document_name',
        'file_url',
        'file_size',
        'uploaded_date',
        'uploaded_by',
        'verified_by',
        'verification_status',
        'rejection_reason'
    ];

    protected $casts = [
        'uploaded_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getDocumentTypeLabelAttribute(): string
    {
        return match($this->document_type) {
            'passport' => 'Pasport',
            'birth_certificate' => 'Tug\'ilganlik guvohnomasi',
            'diplom' => 'Diplom',
            'attestat' => 'Attestat',
            'medical_086' => '086-tibbiy ma\'lumotnoma',
            'medical_063' => '063-tibbiy ma\'lumotnoma',
            'military_ticket' => 'Harbiy guvohnoma',
            'jshshir_certificate' => 'JSHSHIR guvohnomasi',
            'photo_3x4' => '3x4 rasm',
            'privilege_doc' => 'Imtiyoz hujjati',
            'sport_certificate' => 'Sport sertifikati',
            'art_certificate' => 'San\'at sertifikati',
            'recommendation_letter' => 'Tavsiya xati',
            'work_experience' => 'Mehnat daftarchasi',
            'other' => 'Boshqa',
            default => $this->document_type
        };
    }

    public function getVerificationStatusLabelAttribute(): string
    {
        return match($this->verification_status) {
            'pending' => 'Kutilmoqda',
            'verified' => 'Tasdiqlangan',
            'rejected' => 'Rad etilgan',
            default => $this->verification_status
        };
    }

    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function verify(User $verifier): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_by' => $verifier->id,
            'rejection_reason' => null
        ]);
    }

    public function reject(User $verifier, string $reason): void
    {
        $this->update([
            'verification_status' => 'rejected',
            'verified_by' => $verifier->id,
            'rejection_reason' => $reason
        ]);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}