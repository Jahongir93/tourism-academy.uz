<?php

namespace App\Http\Requests\CampusTour;

use Illuminate\Foundation\Http\FormRequest;

class TransportRouteRequest extends FormRequest
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
            'description' => 'nullable|string|max:2000',
            'description_ru' => 'nullable|string|max:2000',
            'description_en' => 'nullable|string|max:2000',
            'type' => 'required|in:bus,metro,walk,taxi,train,other',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'start_point' => 'required|string|max:255',
            'start_point_ru' => 'nullable|string|max:255',
            'start_point_en' => 'nullable|string|max:255',
            'end_point' => 'required|string|max:255',
            'end_point_ru' => 'nullable|string|max:255',
            'end_point_en' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:100',
            'distance' => 'nullable|numeric|min:0|max:1000',
            'price' => 'nullable|numeric|min:0',
            'map_embed_url' => 'nullable|url|max:2000',
            'directions' => 'nullable|string|max:5000',
            'directions_ru' => 'nullable|string|max:5000',
            'directions_en' => 'nullable|string|max:5000',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Yo\'nalish nomi kiritilishi shart',
            'type.required' => 'Transport turi tanlanishi shart',
            'type.in' => 'Noto\'g\'ri transport turi',
            'start_point.required' => 'Boshlang\'ich nuqta kiritilishi shart',
            'end_point.required' => 'Oxirgi nuqta kiritilishi shart',
            'map_embed_url.url' => 'Xarita havolasi noto\'g\'ri formatda',
            'distance.numeric' => 'Masofa son bo\'lishi kerak',
            'price.numeric' => 'Narx son bo\'lishi kerak',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Yo\'nalish nomi',
            'type' => 'Transport turi',
            'start_point' => 'Boshlang\'ich nuqta',
            'end_point' => 'Oxirgi nuqta',
            'duration' => 'Davomiyligi',
            'distance' => 'Masofa',
            'price' => 'Narxi',
            'map_embed_url' => 'Xarita havolasi',
            'directions' => 'Yo\'l-yo\'riq',
        ];
    }
}
