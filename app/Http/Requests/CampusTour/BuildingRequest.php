<?php

namespace App\Http\Requests\CampusTour;

use Illuminate\Foundation\Http\FormRequest;

class BuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'description_ru' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'short_description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'marker_x' => 'nullable|numeric|min:0|max:100',
            'marker_y' => 'nullable|numeric|min:0|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'panorama_id' => 'nullable|exists:campus_tour_panoramas,id',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'working_hours' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'floor_count' => 'nullable|integer|min:1|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Bino nomi kiritilishi shart',
            'title.max' => 'Bino nomi 255 belgidan oshmasligi kerak',
            'marker_x.between' => 'X koordinatasi 0 va 100 orasida bo\'lishi kerak',
            'marker_y.between' => 'Y koordinatasi 0 va 100 orasida bo\'lishi kerak',
            'latitude.between' => 'Kenglik -90 va 90 orasida bo\'lishi kerak',
            'longitude.between' => 'Uzunlik -180 va 180 orasida bo\'lishi kerak',
            'image.max' => 'Rasm hajmi 5MB dan oshmasligi kerak',
            'email.email' => 'Email formati noto\'g\'ri',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Bino nomi',
            'description' => 'Tavsif',
            'marker_x' => 'X koordinatasi',
            'marker_y' => 'Y koordinatasi',
            'latitude' => 'Kenglik',
            'longitude' => 'Uzunlik',
            'working_hours' => 'Ish vaqti',
            'floor_count' => 'Qavatlar soni',
        ];
    }
}
