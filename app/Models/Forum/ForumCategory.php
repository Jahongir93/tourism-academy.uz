<?php

namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }

    public function latestTopic()
    {
        return $this->hasOne(ForumTopic::class, 'category_id')->latest();
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getTopicCountAttribute()
    {
        return $this->topics()->count();
    }

    public function getPostCountAttribute()
    {
        return $this->topics()->withCount('posts')->get()->sum('posts_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}