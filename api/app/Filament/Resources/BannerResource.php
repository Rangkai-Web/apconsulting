<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages\CreateBanner;
use App\Filament\Resources\BannerResource\Pages\EditBanner;
use App\Filament\Resources\BannerResource\Pages\ListBanners;
use App\Models\Banner;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Banners';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banners';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('title')
                ->label('Judul')
                ->placeholder('Masukkan judul banner')
                ->maxLength(255),
            TextInput::make('subtitle')
                ->label('Sub-judul')
                ->placeholder('Masukkan sub-judul/deskripsi pendek')
                ->maxLength(255),
            FileUpload::make('image')
                ->label('Gambar Banner')
                ->disk('public_uploads')
                ->directory('banners')
                ->image()
                ->required()
                ->maxSize(2048)
                ->helperText('Format: JPG, PNG, WEBP. Maksimal 2MB. Rekomendasi rasio: 1600x600 (landskap).'),
            TextInput::make('link')
                ->label('Link / Tautan Redirect')
                ->placeholder('Contoh: /katalog atau https://google.com')
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('Urutan Tampilan')
                ->numeric()
                ->default(0)
                ->required(),
            Toggle::make('is_active')
                ->label('Status Aktif')
                ->default(true)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public_uploads')
                    ->square(),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subtitle')
                    ->label('Sub-judul')
                    ->limit(40)
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif'),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBanners::route('/'),
            'create' => CreateBanner::route('/create'),
            'edit' => EditBanner::route('/{record}/edit'),
        ];
    }
}
