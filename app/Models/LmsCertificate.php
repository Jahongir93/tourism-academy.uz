<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LmsCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'certificate_number',
        'certificate_type',
        'title',
        'description',
        'issue_date',
        'expiry_date',
        'score',
        'grade',
        'metadata',
        'file_path',
        'verification_code',
        'is_verified'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'score' => 'decimal:2',
        'issue_date' => 'date',
        'expiry_date' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = static::generateVerificationCode();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public static function generateCertificateNumber(): string
    {
        $year = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('CERT-%s-%06d', $year, $count);
    }

    public static function generateVerificationCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (static::where('verification_code', $code)->exists());
        
        return $code;
    }

    public function isValid(): bool
    {
        if (!$this->is_verified) {
            return false;
        }
        
        if ($this->expiry_date && $this->expiry_date < now()) {
            return false;
        }
        
        return true;
    }

    public function scopeValid($query)
    {
        return $query->where('is_verified', true)
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }
}