<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Varian';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nama Varian')
                ->required()
                ->maxLength(255)
                ->helperText('Contoh: 250ml, 500ml, 1kg'),
            TextInput::make('sku')
                ->label('SKU')
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('price')
                ->label('Harga (Rp)')
                ->numeric()
                ->required()
                ->minValue(0),
            TextInput::make('stock')
                ->label('Stok')
                ->numeric()
                ->required()
                ->minValue(0)
                ->default(0),
            TextInput::make('weight')
                ->label('Berat (gram)')
                ->numeric()
                ->required()
                ->minValue(1)
                ->helperText('Wajib diisi — dipakai untuk hitung ongkir RajaOngkir.'),
            FileUpload::make('image')
                ->label('Gambar Varian (opsional)')
                ->disk('public_uploads')
                ->directory('variants')
                ->visibility('public')
                ->maxSize(2048)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Kosongkan untuk pakai gambar produk utama.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Gambar')->disk('public_uploads')->circular(),
                TextColumn::make('name')->label('Nama'),
                TextColumn::make('sku')->label('SKU'),
                TextColumn::make('price')->label('Harga')->money('IDR'),
                TextColumn::make('stock')->label('Stok'),
                TextColumn::make('weight')->label('Berat (gr)'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
