<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class FilamentSiteSnippets
{
    protected static bool $shouldCache = true;

    protected static int $cacheTtlInSeconds = 3600;

    protected static function getModelClass(): string
    {
        return Config::get('filament-site-snippets.model', \RectitudeOpen\FilamentSiteSnippets\Models\SiteSnippet::class);
    }

    public static function get(string $key, mixed $default = null): ?string
    {
        $snippet = self::retrieveSnippet($key);

        // @phpstan-ignore-next-line
        return $snippet ? $snippet->content : $default;
    }

    public static function find(string $key): ?Model
    {
        return self::retrieveSnippet($key);
    }

    public static function exists(string $key): bool
    {
        return self::retrieveSnippet($key) !== null;
    }

    public static function all(): Collection
    {
        $cacheKey = 'sitesnippets.all';
        if (self::$shouldCache && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            return is_array($cached) ? collect($cached) : $cached;
        }

        $modelClass = self::getModelClass();
        $snippets = $modelClass::query()->pluck('content', 'key');

        if (self::$shouldCache) {
            Cache::put($cacheKey, $snippets, self::$cacheTtlInSeconds);
        }

        return $snippets;
    }

    protected static function retrieveSnippet(string $key): ?Model
    {
        $cacheKey = "sitesnippet.{$key}";
        if (self::$shouldCache) {
            $cachedSnippet = Cache::get($cacheKey);
            if ($cachedSnippet !== null) {
                return $cachedSnippet === 'not_found_marker' ? null : $cachedSnippet;
            }
        }

        $modelClass = self::getModelClass();
        $snippet = $modelClass::query()->where('key', $key)->first();

        if (self::$shouldCache) {
            Cache::put($cacheKey, $snippet ?? 'not_found_marker', self::$cacheTtlInSeconds);
        }

        return $snippet;
    }

    public static function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("sitesnippet.{$key}");
        }
        Cache::forget('sitesnippets.all');
    }
}
