<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource;

class EditSiteSnippet extends EditRecord
{
    protected static string $resource = SiteSnippetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
