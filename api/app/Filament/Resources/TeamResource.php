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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

use Filament\Schemas\Components\Section;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Tim Profesional';

    protected static ?string $pluralModelLabel = 'Tim Profesional';

    protected static ?string $modelLabel = 'Anggota Tim';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),
            TextInput::make('position')
                ->label('Jabatan / Posisi')
                ->required()
                ->maxLength(255)
                ->placeholder('Contoh: Managing Partner, Senior Tax Consultant'),
            FileUpload::make('photo')
                ->label('Foto Profil')
                ->disk('public_uploads')
                ->directory('team')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Ukuran maksimal: 2MB. Format file: JPG, PNG, atau WEBP.'),
            Textarea::make('bio')
                ->label('Biografi Singkat')
                ->rows(3)
                ->maxLength(500),
            Section::make('Media Sosial')
                ->description('Tautan ke akun media sosial anggota tim.')
                ->schema([
                    TextInput::make('linkedin_url')
                        ->label('LinkedIn URL')
                        ->url()
                        ->placeholder('https://linkedin.com/in/...')
                        ->maxLength(255),
                    TextInput::make('instagram_url')
                        ->label('Instagram URL')
                        ->url()
                        ->placeholder('https://instagram.com/...')
                        ->maxLength(255),
                    TextInput::make('tiktok_url')
                        ->label('TikTok URL')
                        ->url()
                        ->placeholder('https://tiktok.com/@...')
                        ->maxLength(255),
                ])
                ->columns(3),
            TextInput::make('sort_order')
                ->label('Urutan Tampil')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('Aktif Tampil')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order', 'asc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public_uploads')
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
