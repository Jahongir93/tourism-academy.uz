<?php

namespace App\Http\Requests\CampusTour;

use Illuminate\Foundation\Http\FormRequest;

class MapSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'map_type' => 'required|in:image,osm,google',
            'base_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:10240',
            'image_width' => 'nullable|integer|min:100|max:10000',
            'image_height' => 'nullable|integer|min:100|max:10000',
            'center_lat' => 'nullable|numeric|between:-90,90',
            'center_lng' => 'nullable|numeric|between:-180,180',
            'default_zoom' => 'nullable|integer|min:1|max:22',
            'min_zoom' => 'nullable|integer|min:1|max:22',
            'max_zoom' => 'nullable|integer|min:1|max:22',
            'tile_url' => 'nullable|url|max:500',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'map_type.required' => 'Xarita turi tanlanishi shart',
            'map_type.in' => 'Noto\'g\'ri xarita turi',
            'base_image.image' => 'Fayl rasm bo\'lishi kerak',
            'base_image.max' => 'Rasm hajmi 10MB dan oshmasligi kerak',
            'center_lat.between' => 'Kenglik -90 va 90 orasida bo\'lishi kerak',
            'center_lng.between' => 'Uzunlik -180 va 180 orasida bo\'lishi kerak',
        ];
    }
}
