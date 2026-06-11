<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $slug
 * @property string $title
 * @property string $perex
 * @property string $content_markdown
 * @property string $category
 * @property array<int, string>|null $tags
 * @property \Carbon\Carbon|null $published_at
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @method static Builder<Article> published()
 * @method static Builder<Article> byCategory(string $category)
 */
class Article extends Model
{
    use HasUlids;

    protected $fillable = [
        'slug',
        'title',
        'perex',
        'content_markdown',
        'category',
        'tags',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'tags'         => 'array',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * @param Builder<Article> $query
     * @return Builder<Article>
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    public function readingTimeMinutes(): int
    {
        $wordCount = str_word_count(strip_tags($this->content_markdown));

        return (int) max(1, round($wordCount / 200));
    }
}
