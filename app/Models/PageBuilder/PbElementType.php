<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;

class PbElementType extends Model
{
    protected $table = 'pb_element_types';

    protected $fillable = [
        'name',
        'icon',
        'category',
        'default_settings',
        'fields_schema',
        'is_active'
    ];

    protected $casts = [
        'default_settings' => 'array',
        'fields_schema' => 'array',
        'is_active' => 'boolean'
    ];

    public static function getCategories()
    {
        return [
            'basic' => 'Basic Elements',
            'media' => 'Media',
            'advanced' => 'Advanced',
            'forms' => 'Forms',
            'social' => 'Social',
            'commerce' => 'E-Commerce'
        ];
    }
}