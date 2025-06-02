<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource;

class ListSiteSnippets extends ListRecords
{
    protected static string $resource = SiteSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
