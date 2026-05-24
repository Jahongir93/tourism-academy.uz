<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_key',
        'field_type',
        'label_uz',
        'label_ru',
        'label_en',
        'placeholder',
        'options',
        'validation_rules',
        'is_required',
        'step',
        'sort_order',
        'file_config',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'validation_rules' => 'array',
        'file_config' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active fields
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by step and sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('step')->orderBy('sort_order');
    }

    /**
     * Scope for fields by step
     */
    public function scopeByStep($query, int $step)
    {
        return $query->where('step', $step);
    }

    /**
     * Get label based on locale
     */
    public function getLabel(string $locale = 'uz'): string
    {
        $labelField = 'label_' . $locale;
        return $this->$labelField ?? $this->label_uz ?? $this->field_key;
    }

    /**
     * Get validation rules array for Laravel validation
     */
    public function getValidationRulesArray(): array
    {
        $rules = [];

        // Required or nullable
        $rules[] = $this->is_required ? 'required' : 'nullable';

        // Type-specific rules
        switch ($this->field_type) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'phone':
                $rules[] = 'string';
                $rules[] = 'max:20';
                break;
            case 'file':
                $rules[] = 'file';
                $config = $this->file_config ?? [];
                if (isset($config['max_size'])) {
                    $rules[] = 'max:' . $config['max_size'];
                }
                if (isset($config['allowed_extensions']) && is_array($config['allowed_extensions'])) {
                    $rules[] = 'mimes:' . implode(',', $config['allowed_extensions']);
                }
                break;
            case 'textarea':
            case 'text':
                $rules[] = 'string';
                $rules[] = 'max:65535';
                break;
            case 'select':
            case 'radio':
                if (!empty($this->options)) {
                    $rules[] = 'in:' . implode(',', $this->options);
                }
                break;
            case 'checkbox':
                $rules[] = 'array';
                break;
        }

        // Merge custom validation rules
        if (!empty($this->validation_rules)) {
            $rules = array_merge($rules, $this->validation_rules);
        }

        return $rules;
    }

    /**
     * Get accept string for file input
     */
    public function getAcceptString(): string
    {
        if ($this->field_type !== 'file') {
            return '';
        }

        $config = $this->file_config ?? [];
        $extensions = $config['allowed_extensions'] ?? ['pdf', 'jpg', 'jpeg', 'png'];

        $mimeTypes = [];
        foreach ($extensions as $ext) {
            switch (strtolower($ext)) {
                case 'jpg':
                case 'jpeg':
                    $mimeTypes[] = 'image/jpeg';
                    break;
                case 'png':
                    $mimeTypes[] = 'image/png';
                    break;
                case 'gif':
                    $mimeTypes[] = 'image/gif';
                    break;
                case 'pdf':
                    $mimeTypes[] = 'application/pdf';
                    break;
                case 'doc':
                    $mimeTypes[] = 'application/msword';
                    break;
                case 'docx':
                    $mimeTypes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                    break;
                default:
                    $mimeTypes[] = '.' . $ext;
            }
        }

        return implode(',', array_unique($mimeTypes));
    }

    /**
     * Get max file size in MB for display
     */
    public function getMaxFileSizeMB(): float
    {
        $config = $this->file_config ?? [];
        $maxKb = $config['max_size'] ?? 5120; // default 5MB
        return round($maxKb / 1024, 1);
    }

    /**
     * Get storage path for file uploads
     */
    public function getStoragePath(): string
    {
        $config = $this->file_config ?? [];
        return $config['storage_path'] ?? 'admission/uploads';
    }

    /**
     * Check if field is a system field (faculty, specialty)
     */
    public function isSystemField(): bool
    {
        return in_array($this->field_key, ['faculty_id', 'specialty_id']);
    }

    /**
     * Get input type for HTML
     */
    public function getInputType(): string
    {
        switch ($this->field_type) {
            case 'email':
                return 'email';
            case 'phone':
                return 'tel';
            case 'date':
                return 'date';
            case 'file':
                return 'file';
            default:
                return 'text';
        }
    }
}
