<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Resources\TeamResource\Pages\ListTeams;
use App\Models\Team;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Tim Profesional';

    protected static ?string $pluralModelLabel = 'Tim Profesional';

    protected static ?string $modelLabel = 'Anggota Tim';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Biodata & Medsos - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Biodata & Keahlian Konsultan')
                            ->description('Informasi nama resmi, posisi, dan spesialisasi profesional')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap & Gelar')
                                    ->placeholder('Contoh: Adiprimanto, S.E., M.Ak., BKP')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('position')
                                    ->label('Jabatan / Posisi Resmi')
                                    ->placeholder('Contoh: Managing Partner / Senior Tax Advisor')
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('bio')
                                    ->label('Biografi Singkat & Keahlian Khusus')
                                    ->placeholder('Tuliskan ringkasan pengalaman, lisensi Kemenkeu/IKPI, serta spesialisasi (Litigasi Pajak, Transfer Pricing, Tax Planning, Akuntansi)...')
                                    ->rows(4)
                                    ->maxLength(500)
                                    ->helperText('Maksimal 500 karakter. Ditampilkan pada kartu profil tim di website.'),
                            ]),

                        Section::make('Jejaring Profesional & Media Sosial')
                            ->description('Tautan akun profil publik untuk kredibilitas & personal branding')
                            ->icon('heroicon-o-share')
                            ->collapsible()
                            ->schema([
                                TextInput::make('linkedin_url')
                                    ->label('LinkedIn Profile')
                                    ->url()
                                    ->placeholder('https://linkedin.com/in/username')
                                    ->maxLength(255)
                                    ->helperText('Sangat disarankan untuk kredibilitas konsultan pajak & bisnis.'),

                                TextInput::make('instagram_url')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/username')
                                    ->maxLength(255),

                                TextInput::make('twitter_url')
                                    ->label('Twitter / X')
                                    ->url()
                                    ->placeholder('https://x.com/username')
                                    ->maxLength(255),

                                TextInput::make('threads_url')
                                    ->label('Threads')
                                    ->url()
                                    ->placeholder('https://threads.net/@username')
                                    ->maxLength(255),

                                TextInput::make('facebook_url')
                                    ->label('Facebook')
                                    ->url()
                                    ->placeholder('https://facebook.com/username')
                                    ->maxLength(255),

                                TextInput::make('tiktok_url')
                                    ->label('TikTok')
                                    ->url()
                                    ->placeholder('https://tiktok.com/@username')
                                    ->maxLength(255),

                                TextInput::make('youtube_url')
                                    ->label('YouTube Channel')
                                    ->url()
                                    ->placeholder('https://youtube.com/@channel')
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                // ==========================================
                // KOLOM KANAN (Foto & Visibilitas - 1 Kolom)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Foto Profil Resmi')
                            ->description('Format potret/persegi resmi (Rasio 1:1)')
                            ->icon('heroicon-o-camera')
                            ->schema([
                                FileUpload::make('photo')
                                    ->label('Unggah Foto')
                                    ->disk('public_uploads')
                                    ->directory('team')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->helperText('Maks. 2MB (JPG, PNG, WEBP). Gunakan foto latar bersih dengan pakaian formal.'),
                            ]),

                        Section::make('Status & Urutan Tampil')
                            ->description('Pengaturan hierarki di halaman Tim')
                            ->icon('heroicon-o-adjustments-horizontal')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Tampilkan di Website')
                                    ->helperText('Jika nonaktif, profil disembunyikan dari publik.')
                                    ->default(true),

                                TextInput::make('sort_order')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(fn () => (Team::max('sort_order') ?? 0) + 1)
                                    ->required()
                                    ->helperText('Urutan prioritas tim (nomor 1 untuk pimpinan/partner).'),
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

                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public_uploads')
                    ->circular()
                    ->size(46)
                    ->defaultImageUrl(asset('images/redesign/collaboration.png')),

                TextColumn::make('name')
                    ->label('Nama & Jabatan')
                    ->searchable(['name', 'position'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Team $record): string => $record->position)
                    ->wrap(),

                TextColumn::make('linkedin_url')
                    ->label('LinkedIn')
                    ->badge()
                    ->state(fn (Team $record): string => !empty($record->linkedin_url) ? 'Terhubung' : 'Tidak Ada')
                    ->color(fn (string $state): string => $state === 'Terhubung' ? 'info' : 'gray')
                    ->url(fn (Team $record): ?string => $record->linkedin_url)
                    ->openUrlInNewTab()
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
                    ->placeholder('Semua Anggota')
                    ->trueLabel('Hanya yang Aktif')
                    ->falseLabel('Hanya Nonaktif'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
