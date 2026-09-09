<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'question_id',
    'answer_id',
    'category_id',
    'question_en',
    'answer_en',
    'category_en',
    'sort_order',
    'is_active',
])]
class Faq extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
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
            'question' => ($isEn && !empty($this->question_en)) ? $this->question_en : $this->question_id,
            'answer' => ($isEn && !empty($this->answer_en)) ? $this->answer_en : $this->answer_id,
            'category' => ($isEn && !empty($this->category_en)) ? $this->category_en : $this->category_id,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
