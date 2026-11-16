<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceCategoryResource\Pages\CreateServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\EditServiceCategory;
use App\Filament\Resources\ServiceCategoryResource\Pages\ListServiceCategories;
use App\Models\ServiceCategory;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori Layanan';

    protected static ?string $pluralModelLabel = 'Kategori Layanan';

    protected static ?string $modelLabel = 'Kategori Layanan';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Detail Layanan - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Tabs::make('Konten Layanan')
                            ->tabs([
                                // TAB 1: BAHASA INDONESIA
                                Tab::make('Bahasa Indonesia')
                                    ->icon('heroicon-o-language')
                                    ->badge('ID')
                                    ->schema([
                                        TextInput::make('name_id')
                                            ->label('Nama Layanan (ID)')
                                            ->placeholder('Contoh: Kepatuhan & Perencanaan Pajak (Tax Compliance)')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('description_id')
                                            ->label('Deskripsi Layanan (ID)')
                                            ->placeholder('Jelaskan solusi dan nilai tambah layanan ini bagi perusahaan klien...')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Ditampilkan pada kartu layanan di halaman Beranda & Layanan.'),

                                        TextInput::make('cta_text_id')
                                            ->label('Teks Tombol Aksi / CTA (ID)')
                                            ->placeholder('Contoh: KONSULTASI PERPAJAKAN')
                                            ->default('DISKUSIKAN KEBUTUHAN ANDA')
                                            ->maxLength(100),

                                        TagsInput::make('features_id')
                                            ->label('Cakupan / Poin-Poin Layanan (ID)')
                                            ->placeholder('Ketik poin layanan lalu tekan Enter...')
                                            ->helperText('Poin-poin spesifik checklist yang diperoleh klien (contoh: Perencanaan Pajak Bulanan, Pendampingan SP2DK, Pelaporan SPT).'),
                                    ]),

                                // TAB 2: ENGLISH TRANSLATION
                                Tab::make('English Translation')
                                    ->icon('heroicon-o-globe-alt')
                                    ->badge('EN')
                                    ->schema([
                                        TextInput::make('name_en')
                                            ->label('Service Name (EN)')
                                            ->placeholder('Example: Tax Compliance & Strategic Planning')
                                            ->required()
                                            ->maxLength(255),

                                        Textarea::make('description_en')
                                            ->label('Service Description (EN)')
                                            ->placeholder('Describe the scope and business value for international clients...')
                                            ->rows(3)
                                            ->maxLength(500),

                                        TextInput::make('cta_text_en')
                                            ->label('CTA Button Text (EN)')
                                            ->placeholder('Example: DISCUSS TAX ADVISORY')
                                            ->default('DISCUSS YOUR NEEDS')
                                            ->maxLength(100),

                                        TagsInput::make('features_en')
                                            ->label('Service Features / Points (EN)')
                                            ->placeholder('Type item and press Enter...')
                                            ->helperText('English checklist features (example: Monthly Tax Planning, Audit Representation, Annual Return Filing).'),
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
                        Section::make('Status & Urutan Tampil')
                            ->description('Pengaturan visibilitas di website')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Tampilkan di Website')
                                    ->helperText('Jika nonaktif, layanan disembunyikan dari publik.')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(fn () => (ServiceCategory::max('sort_order') ?? 0) + 1)
                                    ->required()
                                    ->helperText('Urutan kartu layanan saat tampil di halaman Beranda & Layanan.'),
                            ]),

                        Section::make('Informasi Tampilan')
                            ->description('Panduan penataan kartu layanan')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Placeholder::make('service_tips')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('
                                        <div style="font-size: 0.8rem; line-height: 1.5; color: #94a3b8;">
                                            💼 <strong>Struktur Kartu Layanan:</strong><br/>
                                            • <strong>Nama &amp; Deskripsi:</strong> Menjadi judul dan penjelasan awal.<br/>
                                            • <strong>Cakupan Poin:</strong> Muncul sebagai checklist keunggulan yang didapat klien.<br/>
                                            • <strong>Tombol CTA:</strong> Mengarahkan langsung ke form kontak konsultasi.
                                        </div>
                                    ')),
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
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('name_id')
                    ->label('Nama Layanan & Deskripsi')
                    ->searchable(['name_id', 'description_id'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (ServiceCategory $record): ?string => $record->description_id ? Str::limit($record->description_id, 75) : null)
                    ->wrap(),

                TextColumn::make('features_id')
                    ->label('Cakupan Poin')
                    ->badge()
                    ->color('info')
                    ->state(function (ServiceCategory $record): string {
                        $count = is_array($record->features_id) ? count($record->features_id) : 0;
                        return $count > 0 ? "{$count} Poin Fitur" : 'Belum Ada';
                    })
                    ->alignCenter(),

                TextColumn::make('name_en')
                    ->label('Versi EN')
                    ->badge()
                    ->state(fn (ServiceCategory $record): string => !empty($record->name_en) ? 'Tersedia' : 'Belum')
                    ->color(fn (string $state): string => $state === 'Tersedia' ? 'success' : 'gray')
                    ->alignCenter(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->onColor('success')
                    ->offColor('gray')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Layanan')
                    ->trueLabel('Hanya yang Aktif')
                    ->falseLabel('Hanya Nonaktif'),
            ])
            ->actions([
                Action::make('view_services')
                    ->label('Lihat di Web')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (): string => url('/services'))
                    ->openUrlInNewTab()
                    ->visible(fn (ServiceCategory $record): bool => (bool) $record->is_active),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceCategories::route('/'),
            'create' => CreateServiceCategory::route('/create'),
            'edit' => EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
