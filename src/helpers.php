<?php

declare(strict_types=1);

use RectitudeOpen\FilamentSiteSnippets\FilamentSiteSnippets;

if (! function_exists('snippet')) {
    function snippet(string $key, mixed $default = null)
    {
        return app(FilamentSiteSnippets::class)::get($key, $default);
    }
}
