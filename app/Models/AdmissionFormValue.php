<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionFormValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'field_key',
        'value',
        'file_path',
    ];

    /**
     * Get the application that owns the value
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(AdmissionApplication::class, 'application_id');
    }

    /**
     * Get the form field definition
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(AdmissionFormField::class, 'field_key', 'field_key');
    }

    /**
     * Get the value, handling JSON for multi-select fields
     */
    public function getParsedValue()
    {
        if (empty($this->value)) {
            return $this->value;
        }

        // Try to decode JSON (for checkbox/multi-select fields)
        $decoded = json_decode($this->value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return $this->value;
    }

    /**
     * Get the display value (for viewing in admin panel)
     */
    public function getDisplayValue(): string
    {
        $parsed = $this->getParsedValue();

        if (is_array($parsed)) {
            return implode(', ', $parsed);
        }

        return (string) ($parsed ?? '');
    }

    /**
     * Check if this value has a file
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Get the file URL
     */
    public function getFileUrl(): ?string
    {
        if (!$this->hasFile()) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the file name from path
     */
    public function getFileName(): ?string
    {
        if (!$this->hasFile()) {
            return null;
        }

        return basename($this->file_path);
    }
}
