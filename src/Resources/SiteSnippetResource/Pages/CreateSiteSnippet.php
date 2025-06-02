<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource;

class CreateSiteSnippet extends CreateRecord
{
    protected static string $resource = SiteSnippetResource::class;
}
