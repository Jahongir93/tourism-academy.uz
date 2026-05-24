<?php

namespace App\Http\Requests\CampusTour;

use Illuminate\Foundation\Http\FormRequest;

class PanoramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $panoramaId = $this->route('panorama');
        $imageRule = $panoramaId ? 'nullable' : 'required';

        return [
            'title' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'description_ru' => 'nullable|string|max:2000',
            'description_en' => 'nullable|string|max:2000',
            'image' => [
                $imageRule,
                'image',
                'mimes:jpeg,jpg,png',
                'max:20480', // 20MB
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $image = getimagesize($value->getRealPath());
                        if ($image) {
                            $width = $image[0];
                            $height = $image[1];
                            $ratio = $width / $height;
                            // Check for 2:1 aspect ratio (equirectangular)
                            if ($ratio < 1.9 || $ratio > 2.1) {
                                $fail('360° panorama uchun 2:1 nisbatdagi rasm kerak (equirectangular format).');
                            }
                        }
                    }
                },
            ],
            'building_id' => 'nullable|exists:campus_tour_buildings,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'hotspots' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Panorama nomi kiritilishi shart',
            'title.max' => 'Panorama nomi 255 belgidan oshmasligi kerak',
            'image.required' => '360° panorama rasmi yuklanishi shart',
            'image.image' => 'Fayl rasm bo\'lishi kerak',
            'image.mimes' => 'Rasm formati: JPEG, JPG yoki PNG',
            'image.max' => 'Rasm hajmi 20MB dan oshmasligi kerak',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Nomi',
            'title_ru' => 'Nomi (rus)',
            'title_en' => 'Nomi (ingliz)',
            'description' => 'Tavsif',
            'image' => 'Panorama rasmi',
            'building_id' => 'Bino',
            'order' => 'Tartib',
        ];
    }
}
