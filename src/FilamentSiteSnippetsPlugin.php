<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets;

use Filament\Contracts\Plugin;
use Filament\Panel;
use RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource;

class FilamentSiteSnippetsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-site-snippets';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                config('filament-site-snippets.filament_resource', SiteSnippetResource::class),
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
