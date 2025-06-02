<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use RectitudeOpen\FilamentSiteSnippets\Models\SiteSnippet;
use RectitudeOpen\FilamentSiteSnippets\Resources\SiteSnippetResource\Pages;

class SiteSnippetResource extends Resource
{
    public static function getModel(): string
    {
        return config('filament-site-snippets.model', SiteSnippet::class);
    }

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon ?? config('filament-site-snippets.navigation_icon', 'heroicon-o-puzzle-piece');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-site-snippets.navigation_sort', 0);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.content');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema(function (callable $get): array {
                        $type = $get('type');
                        $contentFieldName = 'content';
                        switch ($type) {
                            case 'text':
                                return [
                                    Forms\Components\Textarea::make($contentFieldName)
                                        ->label('Content (Plain Text)')
                                        ->required()
                                        ->maxLength(255)
                                        ->autosize(),
                                ];
                            case 'html':
                                return [
                                    Forms\Components\RichEditor::make($contentFieldName)
                                        ->label('Content (HTML)')
                                        ->maxLength(255)
                                        ->required(),
                                ];
                            case 'image':
                                return [
                                    Forms\Components\FileUpload::make($contentFieldName)
                                        ->label('Image File')
                                        ->image()
                                        ->disk('public')
                                        ->directory('snippets')
                                        ->required(),
                                ];
                            default:
                                return [
                                    Forms\Components\Placeholder::make('content_type_notice_' . uniqid())
                                        ->content('Please select a content type to enter content.'),

                                    Forms\Components\Hidden::make($contentFieldName)->default(null),
                                ];
                        }
                    })
                    ->key('dynamic_content_section_' . uniqid())
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            return $rule->whereNull('deleted_at');
                        }
                    )
                    ->disabledOn('edit'),
                Forms\Components\Select::make('type')
                    ->options([
                        'text' => 'Plain Text',
                        'html' => 'HTML Content',
                        'image' => 'Image',
                    ])
                    ->required()
                    ->default('text')
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set, $state) => $set('content', null)),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->limit(255)
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(function (?string $state, SiteSnippet $record): string {
                        if ($record->type === 'image') {
                            if (is_string($state) && ! empty($state)) {
                                return '<img src="' . Storage::disk('public')->url($state) . '" style="max-height: 40px; max-width: 100px; border-radius: 0.25rem; object-fit: cover;" alt="" />';
                            }

                            return '';
                        } elseif ($record->type === 'html') {
                            return Str::limit(strip_tags((string) $state), 50);
                        }

                        return Str::limit((string) $state, 50);
                    })
                    ->html(fn (SiteSnippet $record) => $record->type === 'image')
                    ->tooltip(fn (SiteSnippet $record) => $record->content),
                Tables\Columns\TextColumn::make('key')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSnippets::route('/'),
            'create' => Pages\CreateSiteSnippet::route('/create'),
            'edit' => Pages\EditSiteSnippet::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
