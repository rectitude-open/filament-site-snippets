<?php

declare(strict_types=1);

namespace RectitudeOpen\FilamentSiteSnippets\Resources;

use Filament\Forms;
use Filament\Forms\Components\RichEditor;
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

    public static function getNavigationLabel(): string
    {
        return __('filament-site-snippets::filament-site-snippets.nav.label');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-site-snippets::filament-site-snippets.nav.group');
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
                                        ->label(__('filament-site-snippets::filament-site-snippets.field.plain_text'))
                                        ->required()
                                        ->maxLength(65535)
                                        ->autosize(),
                                ];
                            case 'html':
                                $editorClass = config('filament-site-snippets.editor_component_class', RichEditor::class);
                                $editorComponent = $editorClass::make($contentFieldName)
                                    ->label(__('filament-site-snippets::filament-site-snippets.field.html'))
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('uploads')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpan('full')
                                    ->maxLength(65535)
                                    ->required();

                                return [$editorComponent];
                            case 'image':
                                return [
                                    Forms\Components\FileUpload::make($contentFieldName)
                                        ->label(__('filament-site-snippets::filament-site-snippets.field.image'))
                                        ->image()
                                        ->disk('public')
                                        ->directory('snippets')
                                        ->required(),
                                ];
                            default:
                                return [
                                    Forms\Components\Placeholder::make('content_type_notice_' . uniqid())
                                        ->content(__('filament-site-snippets::filament-site-snippets.info.type_notice')),

                                    Forms\Components\Hidden::make($contentFieldName)->default(null),
                                ];
                        }
                    })
                    ->key('dynamic_content_section_' . uniqid())
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('key')
                    ->label(__('filament-site-snippets::filament-site-snippets.field.key'))
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
                    ->label(__('filament-site-snippets::filament-site-snippets.field.type'))
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
                    ->label(__('filament-site-snippets::filament-site-snippets.field.description'))
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament-site-snippets::filament-site-snippets.field.description'))
                    ->limit(255)
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->label(__('filament-site-snippets::filament-site-snippets.field.content'))
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
                    ->label(__('filament-site-snippets::filament-site-snippets.field.key'))
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament-site-snippets::filament-site-snippets.field.type'))
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-site-snippets::filament-site-snippets.field.updated_at'))
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
