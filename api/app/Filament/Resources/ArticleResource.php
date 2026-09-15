<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Filament\Resources\ArticleResource\Pages\ListArticles;
use App\Models\Article;
use App\Models\ServiceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Artikel & Insight';

    protected static ?string $pluralModelLabel = 'Artikel & Insight';

    protected static ?string $modelLabel = 'Artikel';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            FileUpload::make('cover_image')
                ->label('Gambar Sampul (Cover)')
                ->disk('public_uploads')
                ->directory('articles')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Ukuran maksimal: 2MB. Format file: JPG, PNG, atau WEBP.'),
            TextInput::make('read_time')
                ->label('Estimasi Waktu Baca')
                ->numeric()
                ->suffix('Menit')
                ->default(5)
                ->minValue(1)
                ->required(),
            DatePicker::make('published_at')
                ->label('Tanggal Terbit')
                ->default(now()),
            Toggle::make('is_published')
                ->label('Publikasikan')
                ->default(true),

            Tabs::make('Konten Bahasa')
                ->tabs([
                    Tab::make('Bahasa Indonesia')
                        ->icon('heroicon-o-language')
                        ->schema([
                            TextInput::make('title_id')
                                ->label('Judul Artikel (ID)')
                                ->required()
                                ->maxLength(255)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                                    if ($operation === 'create') {
                                        $set('slug_id', Str::slug($state ?? ''));
                                    }
                                }),
                            TextInput::make('slug_id')
                                ->label('Slug URL (ID)')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('category_id')
                                ->label('Kategori (Bahasa Indonesia)')
                                ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_id', 'name_id')->toArray())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    if ($state) {
                                        $cat = ServiceCategory::where('name_id', $state)->first();
                                        if ($cat) {
                                            $set('category_en', $cat->name_en);
                                        }
                                    }
                                }),
                            Textarea::make('excerpt_id')
                                ->label('Ringkasan Singkat (ID)')
                                ->rows(3)
                                ->maxLength(500),
                            RichEditor::make('content_id')
                                ->label('Konten Artikel (ID)')
                                ->required()
                                ->columnSpan(2),
                        ]),
                    Tab::make('English')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            TextInput::make('title_en')
                                ->label('Article Title (EN)')
                                ->maxLength(255)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                                    if ($operation === 'create' && !empty($state)) {
                                        $set('slug_en', Str::slug($state));
                                    }
                                }),
                            TextInput::make('slug_en')
                                ->label('Slug URL (EN)')
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),
                            Select::make('category_en')
                                ->label('Category (English)')
                                ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_en', 'name_en')->toArray())
                                ->searchable()
                                ->preload(),
                            Textarea::make('excerpt_en')
                                ->label('Short Excerpt (EN)')
                                ->rows(3)
                                ->maxLength(500),
                            RichEditor::make('content_en')
                                ->label('Article Content (EN)')
                                ->columnSpan(2),
                        ]),
                ])
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->disk('public_uploads')
                    ->square(),
                TextColumn::make('title_id')
                    ->label('Judul (ID)')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                TextColumn::make('category_id')
                    ->label('Kategori (ID)')
                    ->badge()
                    ->searchable(),
                TextColumn::make('read_time')
                    ->label('Waktu Baca')
                    ->suffix(' Menit'),
                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
                TextColumn::make('views_count')
                    ->label('Dilihat')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Tgl Terbit')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Status Terbit'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
        ];
    }
}
