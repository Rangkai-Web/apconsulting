<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title_id',
    'slug_id',
    'excerpt_id',
    'content_id',
    'category_id',
    'title_en',
    'slug_en',
    'excerpt_en',
    'content_en',
    'category_en',
    'cover_image',
    'author_name',
    'read_time',
    'published_at',
    'is_published',
    'views_count',
])]
class Article extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'date',
            'read_time' => 'integer',
            'views_count' => 'integer',
        ];
    }

    /**
     * Map model attributes dynamically for locale ('id' or 'en')
     */
    public function toLocalizedArray(string $locale = 'id'): array
    {
        $isEn = strtolower($locale) === 'en';

        return [
            'id' => $this->id,
            'title' => ($isEn && !empty($this->title_en)) ? $this->title_en : $this->title_id,
            'slug' => ($isEn && !empty($this->slug_en)) ? $this->slug_en : $this->slug_id,
            'excerpt' => ($isEn && !empty($this->excerpt_en)) ? $this->excerpt_en : $this->excerpt_id,
            'content' => ($isEn && !empty($this->content_en)) ? $this->content_en : $this->content_id,
            'category' => ($isEn && !empty($this->category_en)) ? $this->category_en : $this->category_id,
            'cover_image' => $this->cover_image,
            'author_name' => $this->author_name,
            'read_time' => (int) ($this->read_time ?? 5),
            'published_at' => $this->published_at ? $this->published_at->format('Y-m-d') : null,
            'is_published' => $this->is_published,
            'views_count' => $this->views_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
