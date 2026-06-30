<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    protected static ?int $navigationSort = 5;

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'pending' => 'gray',
            'paid' => 'info',
            'processing' => 'warning',
            'shipped' => 'primary',
            'completed' => 'success',
            'cancelled', 'expired' => 'danger',
            default => 'gray',
        };
    }

    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    public static function formatCourier(Order $record): string
    {
        return $record->courier ? strtoupper($record->courier).' - '.$record->courier_service : '-';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer')
                    ->label('Pelanggan')
                    ->state(fn (Order $record): string => $record->user?->name ?? $record->guest_name ?? '-'),
                TextColumn::make('customer_type')
                    ->label('Tipe')
                    ->state(fn (Order $record): string => $record->user_id ? 'Member' : 'Guest')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Member' ? 'success' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR'),
                TextColumn::make('courier')
                    ->label('Kurir')
                    ->formatStateUsing(fn (Order $record): string => self::formatCourier($record)),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(self::statusOptions()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pesanan')
                ->schema([
                    TextEntry::make('order_number')->label('No. Pesanan'),
                    TextEntry::make('status')->label('Status')->badge()->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('created_at')->label('Tanggal Pesanan')->dateTime('d M Y H:i'),
                    TextEntry::make('courier')
                        ->label('Kurir')
                        ->formatStateUsing(fn (Order $record): string => self::formatCourier($record)),
                    TextEntry::make('tracking_number')->label('No. Resi')->placeholder('-'),
                    TextEntry::make('shipped_at')->label('Dikirim Pada')->dateTime('d M Y H:i')->placeholder('-'),
                    TextEntry::make('completed_at')->label('Selesai Pada')->dateTime('d M Y H:i')->placeholder('-'),
                ])
                ->columns(2),
            Section::make('Pelanggan')
                ->schema([
                    TextEntry::make('customer_name')->label('Nama')->state(fn (Order $record): string => $record->user?->name ?? $record->guest_name ?? '-'),
                    TextEntry::make('customer_email')->label('Email')->state(fn (Order $record): string => $record->user?->email ?? $record->guest_email ?? '-'),
                    TextEntry::make('customer_phone')->label('Telepon')->state(fn (Order $record): string => $record->user?->phone ?? $record->guest_phone ?? '-'),
                    TextEntry::make('customer_type')->label('Tipe')->state(fn (Order $record): string => $record->user_id ? 'Member' : 'Guest')->badge(),
                ])
                ->columns(2),
            Section::make('Alamat Pengiriman')
                ->schema([
                    TextEntry::make('shipping_address.recipient_name')->label('Penerima'),
                    TextEntry::make('shipping_address.phone')->label('Telepon'),
                    TextEntry::make('shipping_address.full_address')->label('Alamat Lengkap')->columnSpanFull(),
                    TextEntry::make('shipping_address.district')->label('Kecamatan'),
                    TextEntry::make('shipping_address.city')->label('Kota/Kabupaten'),
                    TextEntry::make('shipping_address.province')->label('Provinsi'),
                    TextEntry::make('shipping_address.postal_code')->label('Kode Pos'),
                ])
                ->columns(2),
            Section::make('Item Pesanan')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            TextEntry::make('product_name')->label('Produk'),
                            TextEntry::make('variant_name')->label('Varian'),
                            TextEntry::make('qty')->label('Qty'),
                            TextEntry::make('price')->label('Harga')->money('IDR'),
                            TextEntry::make('subtotal')->label('Subtotal')->money('IDR'),
                        ])
                        ->columns(5),
                    TextEntry::make('subtotal')->label('Subtotal Pesanan')->money('IDR'),
                    TextEntry::make('shipping_cost')->label('Ongkos Kirim')->money('IDR'),
                    TextEntry::make('total')->label('Total')->money('IDR')->weight('bold'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
