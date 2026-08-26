<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages\CreatePaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\EditPaymentMethod;
use App\Filament\Resources\PaymentMethodResource\Pages\ListPaymentMethods;
use App\Models\PaymentMethod;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Metode Pembayaran';

    protected static ?string $modelLabel = 'Metode Pembayaran';

    protected static ?string $pluralModelLabel = 'Metode Pembayaran';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('label')
                ->label('Nama Metode')
                ->placeholder('Contoh: BCA, ShopeePay, QRIS')
                ->required()
                ->maxLength(255),
            TextInput::make('account_number')
                ->label('Nomor Rekening / Nomor Akun')
                ->maxLength(255),
            TextInput::make('account_holder')
                ->label('Atas Nama')
                ->maxLength(255),
            FileUpload::make('qr_image')
                ->label('Gambar QR (Opsional)')
                ->disk('public_uploads')
                ->directory('payment-methods')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(2048)
                ->helperText('Format: JPG, PNG, WEBP. Maksimal 2MB.'),
            Textarea::make('instructions')
                ->label('Instruksi Tambahan')
                ->rows(3),
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
                ImageColumn::make('qr_image')
                    ->label('QR')
                    ->disk('public_uploads')
                    ->square(),
                TextColumn::make('label')
                    ->label('Nama Metode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_number')
                    ->label('Nomor Rekening/Akun'),
                TextColumn::make('account_holder')
                    ->label('Atas Nama'),
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
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentMethods::route('/'),
            'create' => CreatePaymentMethod::route('/create'),
            'edit' => EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
