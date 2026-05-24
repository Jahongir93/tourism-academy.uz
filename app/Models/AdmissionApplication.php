<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdmissionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_number',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'gender',
        'passport_series',
        'passport_number',
        'jshshir',
        'phone',
        'email',
        'region',
        'district',
        'address',
        'education_type',
        'education_name',
        'graduation_year',
        'dtm_score',
        'faculty_id',
        'specialty_id',
        'education_form',
        'education_language',
        'photo_path',
        'passport_copy_path',
        'diploma_copy_path',
        'certificate_copy_path',
        'status',
        'applied_at',
        'reviewed_by',
        'reviewed_at',
        'notes',
        'form_data',
        'form_version',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'applied_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'graduation_year' => 'integer',
        'dtm_score' => 'integer',
        'form_data' => 'array',
        'form_version' => 'integer',
    ];

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        return $this->last_name . ' ' . $this->first_name . ' ' . ($this->middle_name ?? '');
    }

    /**
     * Get age
     */
    public function getAgeAttribute()
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'reviewing' => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            'waitlist' => 'secondary'
        ][$this->status] ?? 'secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'Kutilmoqda',
            'reviewing' => 'Ko\'rib chiqilmoqda',
            'accepted' => 'Qabul qilindi',
            'rejected' => 'Rad etildi',
            'waitlist' => 'Kutish ro\'yxatida'
        ][$this->status] ?? 'Noma\'lum';
    }

    /**
     * Get education form text
     */
    public function getEducationFormTextAttribute()
    {
        return [
            'kunduzgi' => 'Kunduzgi',
            'sirtqi' => 'Sirtqi',
            'kechki' => 'Kechki'
        ][$this->education_form] ?? $this->education_form;
    }

    /**
     * Get education language text
     */
    public function getEducationLanguageTextAttribute()
    {
        return [
            'uz' => 'O\'zbek',
            'ru' => 'Rus',
            'en' => 'Ingliz'
        ][$this->education_language] ?? $this->education_language;
    }

    /**
     * Get education type text
     */
    public function getEducationTypeTextAttribute()
    {
        return [
            'school' => 'Maktab',
            'college' => 'Kollej',
            'lyceum' => 'Litsey',
            'bachelor' => 'Bakalavr'
        ][$this->education_type] ?? $this->education_type;
    }

    /**
     * Relationships
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get dynamic form values
     */
    public function formValues(): HasMany
    {
        return $this->hasMany(AdmissionFormValue::class, 'application_id');
    }

    /**
     * Get a specific field value
     */
    public function getFieldValue(string $fieldKey): ?string
    {
        // First check form_data JSON
        if (!empty($this->form_data) && isset($this->form_data[$fieldKey])) {
            $value = $this->form_data[$fieldKey];
            return is_array($value) ? json_encode($value) : $value;
        }

        // Then check formValues relationship
        $formValue = $this->formValues()->where('field_key', $fieldKey)->first();
        return $formValue ? $formValue->value : null;
    }

    /**
     * Get a specific field file path
     */
    public function getFieldFilePath(string $fieldKey): ?string
    {
        $formValue = $this->formValues()->where('field_key', $fieldKey)->first();
        return $formValue ? $formValue->file_path : null;
    }

    /**
     * Get all field values as key-value array
     */
    public function getAllFieldValues(): array
    {
        $values = [];

        // Start with form_data if available
        if (!empty($this->form_data)) {
            $values = $this->form_data;
        }

        // Add/override with formValues
        foreach ($this->formValues as $formValue) {
            $values[$formValue->field_key] = $formValue->getParsedValue();
        }

        return $values;
    }

    /**
     * Get all field file paths as key-value array
     */
    public function getAllFilePaths(): array
    {
        $paths = [];
        foreach ($this->formValues as $formValue) {
            if ($formValue->hasFile()) {
                $paths[$formValue->field_key] = $formValue->file_path;
            }
        }
        return $paths;
    }
}