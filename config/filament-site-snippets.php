<?php

declare(strict_types=1);

return [
    'filament_resource' => RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource::class,
    'model' => RectitudeOpen\FilamentSiteSnippets\Models\SiteSnippet::class,
    'navigation_sort' => 0,
    'navigation_icon' => 'heroicon-o-puzzle-piece',
    'editor_component_class' => \Filament\Forms\Components\RichEditor::class,
];
