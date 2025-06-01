<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RectitudeOpen\FilamentSiteSnippets\FilamentSiteSnippets
 */
class FilamentSiteSnippets extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RectitudeOpen\FilamentSiteSnippets\FilamentSiteSnippets::class;
    }
}
