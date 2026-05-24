<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PbPage extends Model
{
    protected $table = 'pb_pages';

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'meta_keywords',
        'status',
        'settings',
        'created_by'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PbSection::class, 'page_id')->orderBy('order');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PbRevision::class, 'page_id')->orderBy('created_at', 'desc');
    }

    public function assets(): HasOne
    {
        return $this->hasOne(PbPageAsset::class, 'page_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function duplicate()
    {
        $newPage = $this->replicate();
        $newPage->slug = $this->slug . '-copy-' . time();
        $newPage->title = $this->title . ' (Copy)';
        $newPage->status = 'draft';
        $newPage->save();

        foreach ($this->sections as $section) {
            $newSection = $section->replicate();
            $newSection->page_id = $newPage->id;
            $newSection->save();

            foreach ($section->columns as $column) {
                $newColumn = $column->replicate();
                $newColumn->section_id = $newSection->id;
                $newColumn->save();

                foreach ($column->elements as $element) {
                    $newElement = $element->replicate();
                    $newElement->column_id = $newColumn->id;
                    $newElement->save();
                }
            }
        }

        return $newPage;
    }
}