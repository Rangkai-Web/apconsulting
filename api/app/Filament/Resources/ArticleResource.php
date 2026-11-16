<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Resources\ArticleResource\Pages\EditArticle;
use App\Filament\Resources\ArticleResource\Pages\ListArticles;
use App\Models\Article;
use App\Models\ServiceCategory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
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
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Konten Utama - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Tabs::make('Konten Bahasa')
                            ->tabs([
                                // TAB 1: BAHASA INDONESIA
                                Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-o-language')
                                    ->badge('ID')
                                    ->schema([
                                        TextInput::make('title_id')
                                            ->label('Judul Artikel (ID)')
                                            ->placeholder('Contoh: Panduan Lengkap Tarif Pajak dan Kepatuhan SPT Tahunan...')
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
                                            ->prefix('insight/')
                                            ->helperText('URL ramah mesin pencari (SEO). Digenerate otomatis dari judul.')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Select::make('category_id')
                                            ->label('Kategori Layanan (ID)')
                                            ->placeholder('Pilih kategori layanan terkait...')
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
                                            ->label('Ringkasan Singkat / Excerpt (ID)')
                                            ->placeholder('Tuliskan ringkasan singkat artikel (120-160 karakter untuk hasil SEO terbaik & preview WhatsApp)...')
                                            ->rows(3)
                                            ->maxLength(255)
                                            ->helperText('Cuplikan ini akan muncul di kartu artikel dan hasil pencarian Google.'),

                                        RichEditor::make('content_id')
                                            ->label('Konten Lengkap Artikel (ID)')
                                            ->required()
                                            ->fileAttachmentsDisk('public_uploads')
                                            ->fileAttachmentsDirectory('articles/content'),
                                    ]),

                                // TAB 2: ENGLISH TRANSLATION
                                Tab::make('English Translation')
                                    ->icon('heroicon-o-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        TextInput::make('title_en')
                                            ->label('Article Title (EN)')
                                            ->placeholder('Example: Comprehensive Guide to Indonesia Tax Compliance...')
                                            ->maxLength(255)
                                            ->live(debounce: 500)
                                            ->afterStateUpdated(function (string $operation, Set $set, ?string $state) {
                                                if ($operation === 'create' && !empty($state)) {
                                                    $set('slug_en', Str::slug($state));
                                                }
                                            }),

                                        TextInput::make('slug_en')
                                            ->label('Slug URL (EN)')
                                            ->prefix('en/insight/')
                                            ->helperText('Permanent URL for English version.')
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        Select::make('category_en')
                                            ->label('Service Category (EN)')
                                            ->placeholder('Select English category...')
                                            ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_en', 'name_en')->toArray())
                                            ->searchable()
                                            ->preload(),

                                        Textarea::make('excerpt_en')
                                            ->label('Short Excerpt (EN)')
                                            ->placeholder('Brief article excerpt for English readers and search engines...')
                                            ->rows(3)
                                            ->maxLength(255)
                                            ->helperText('Recommended 120-160 characters for optimal Google search snippets.'),

                                        RichEditor::make('content_en')
                                            ->label('Article Content (EN)')
                                            ->fileAttachmentsDisk('public_uploads')
                                            ->fileAttachmentsDirectory('articles/content'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                // ==========================================
                // KOLOM KANAN (Sidebar Form - 1 Kolom)
                // ==========================================
                Group::make()
                    ->schema([
                        // 1. Status & Visibilitas
                        Section::make('Publikasi & Visibilitas')
                            ->description('Atur status ketersediaan artikel')
                            ->icon('heroicon-o-signal')
                            ->schema([
                                Toggle::make('is_published')
                                    ->label('Terbitkan ke Publik')
                                    ->helperText('Jika dinonaktifkan, artikel berstatus Draft.')
                                    ->default(true),

                                DatePicker::make('published_at')
                                    ->label('Tanggal Publikasi')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('read_time')
                                    ->label('Estimasi Waktu Baca')
                                    ->numeric()
                                    ->suffix('Menit')
                                    ->default(5)
                                    ->minValue(1)
                                    ->required()
                                    ->helperText('Rata-rata 200 kata = 1 menit.'),
                            ]),

                        // 2. Gambar Sampul
                        Section::make('Gambar Sampul (Cover)')
                            ->description('Foto utama artikel (Rasio 16:9)')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('cover_image')
                                    ->label('Unggah Sampul')
                                    ->disk('public_uploads')
                                    ->directory('articles')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('Maks. 2MB. Resolusi ideal: 1200x675 px (JPG, PNG, WEBP).'),
                            ]),

                        // 3. Atribusi & Penulis
                        Section::make('Penulis & Metadata')
                            ->description('Informasi kontributor artikel')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('author_name')
                                    ->label('Nama Penulis / Tim')
                                    ->placeholder('Contoh: Tim Ahli Pajak AP Consulting')
                                    ->default('AP Consulting Team')
                                    ->maxLength(100),

                                Placeholder::make('views_count')
                                    ->label('Total Pembaca')
                                    ->content(fn (?Article $record): string => $record ? number_format($record->views_count) . ' kali dilihat' : '0 kali (Artikel Baru)')
                                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
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
                    ->height(46)
                    ->width(76)
                    ->extraImgAttributes(['style' => 'object-fit: cover; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);'])
                    ->defaultImageUrl(asset('images/redesign/article-featured.png')),

                TextColumn::make('title_id')
                    ->label('Judul Artikel')
                    ->searchable()
                    ->sortable()
                    ->limit(55)
                    ->tooltip(fn (Article $record): string => $record->title_id)
                    ->description(fn (Article $record): ?string => $record->excerpt_id ? Str::limit($record->excerpt_id, 65) : null)
                    ->wrap(),

                TextColumn::make('category_id')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                ToggleColumn::make('is_published')
                    ->label('Terbit')
                    ->onColor('success')
                    ->offColor('gray')
                    ->sortable(),

                TextColumn::make('views_count')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('read_time')
                    ->label('Waktu')
                    ->suffix(' mnt')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('published_at')
                    ->label('Tgl Terbit')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Status Terbit')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya yang Terbit')
                    ->falseLabel('Hanya Draft'),

                SelectFilter::make('category_id')
                    ->label('Kategori Layanan')
                    ->options(fn () => ServiceCategory::where('is_active', true)->pluck('name_id', 'name_id')->toArray()),
            ])
            ->actions([
                Action::make('view_website')
                    ->label('Lihat di Web')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Article $record): string => url('/insight/' . ($record->slug_id ?: $record->id)))
                    ->openUrlInNewTab()
                    ->visible(fn (Article $record): bool => (bool) $record->is_published),

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
