<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use RectitudeOpen\FilamentSiteSnippets\FilamentSiteSnippets;

/**
 * @property int $id
 * @property string $key
 * @property string $content
 * @property string $type
 * @property string|null $description
 */
class SiteSnippet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'key',
        'content',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(function (SiteSnippet $snippet) {
            FilamentSiteSnippets::clearCache($snippet->key);
        });

        static::deleted(function (SiteSnippet $snippet) {
            FilamentSiteSnippets::clearCache($snippet->key);
        });
    }
}
