<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\ViewOrder;
use App\Models\Order;
use Filament\Infolists\Components\ImageEntry;
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
            'processing' => 'warning',
            'ordered' => 'info',
            'arrived_at_proxy' => 'info',
            'shipped_to_indonesia' => 'warning',
            'arrived_at_indonesia' => 'warning',
            'ready_to_ship' => 'primary',
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
            'processing' => 'Processing',
            'ordered' => 'Ordered (PO)',
            'arrived_at_proxy' => 'Arrived at Warehouse Proxy',
            'shipped_to_indonesia' => 'Shipped to Indonesia',
            'arrived_at_indonesia' => 'Arrived at Warehouse Indonesia',
            'ready_to_ship' => 'Ready to Ship (Pending Balance)',
            'shipped' => 'Shipped',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
        ];
    }

    public static function paymentStatusColor(string $status): string
    {
        return match ($status) {
            'unpaid' => 'gray',
            'awaiting_verification' => 'warning',
            'paid' => 'success',
            'rejected' => 'danger',
            'refunded' => 'info',
            default => 'gray',
        };
    }

    public static function paymentStatusOptions(): array
    {
        return [
            'unpaid' => 'Unpaid',
            'awaiting_verification' => 'Menunggu Verifikasi',
            'paid' => 'Paid',
            'rejected' => 'Rejected',
            'refunded' => 'Refunded',
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
                TextColumn::make('order_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => self::paymentStatusColor($state)),
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
                SelectFilter::make('order_status')
                    ->label('Status')
                    ->options(self::statusOptions()),
                SelectFilter::make('payment_status')
                    ->label('Pembayaran')
                    ->options(self::paymentStatusOptions()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pesanan')
                ->schema([
                    TextEntry::make('order_number')->label('No. Pesanan'),
                    TextEntry::make('order_status')->label('Status')->badge()->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('created_at')->label('Tanggal Pesanan')->dateTime('d M Y H:i'),
                    TextEntry::make('courier')
                        ->label('Kurir')
                        ->formatStateUsing(fn (Order $record): string => self::formatCourier($record)),
                    TextEntry::make('tracking_number')->label('No. Resi')->placeholder('-'),
                    TextEntry::make('shipped_at')->label('Dikirim Pada')->dateTime('d M Y H:i')->placeholder('-'),
                    TextEntry::make('completed_at')->label('Selesai Pada')->dateTime('d M Y H:i')->placeholder('-'),
                ])
                ->columns(2),
            Section::make('Pembayaran')
                ->schema([
                    TextEntry::make('payment_status')
                        ->label('Status Pembayaran')
                        ->badge()
                        ->color(fn (string $state): string => self::paymentStatusColor($state)),
                    TextEntry::make('payment_rejected_reason')
                        ->label('Alasan Ditolak')
                        ->placeholder('-'),
                    ImageEntry::make('dp_payment_proof')
                        ->label('Bukti Transfer DP')
                        ->disk('public_uploads')
                        ->height(250)
                        ->visible(fn (Order $record): bool => filled($record->dp_payment_proof))
                        ->action(
                            \Filament\Actions\Action::make('view_dp_proof')
                                ->modalHeading('Bukti Transfer DP')
                                ->modalSubmitAction(false)
                                ->modalCancelAction(false)
                                ->modalWidth('lg')
                                ->form([
                                    \Filament\Forms\Components\Placeholder::make('dp_proof_image')
                                        ->hiddenLabel()
                                        ->columnSpanFull()
                                        ->content(fn (Order $record) => new \Illuminate\Support\HtmlString(
                                            '<div class="flex justify-center w-full"><img src="' . \Illuminate\Support\Facades\Storage::disk('public_uploads')->url($record->dp_payment_proof) . '" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-md border border-gray-100 mx-auto" /></div>'
                                        ))
                                ])
                        ),
                    ImageEntry::make('payment_proof')
                        ->label(fn (Order $record): string => filled($record->dp_payment_proof) ? 'Bukti Transfer Pelunasan' : 'Bukti Transfer')
                        ->disk('public_uploads')
                        ->height(250)
                        ->visible(fn (Order $record): bool => filled($record->payment_proof))
                        ->action(
                            \Filament\Actions\Action::make('view_proof')
                                ->modalHeading(fn (Order $record): string => filled($record->dp_payment_proof) ? 'Bukti Transfer Pelunasan' : 'Bukti Transfer')
                                ->modalSubmitAction(false)
                                ->modalCancelAction(false)
                                ->modalWidth('lg')
                                ->form([
                                    \Filament\Forms\Components\Placeholder::make('proof_image')
                                        ->hiddenLabel()
                                        ->columnSpanFull()
                                        ->content(fn (Order $record) => new \Illuminate\Support\HtmlString(
                                            '<div class="flex justify-center w-full"><img src="' . \Illuminate\Support\Facades\Storage::disk('public_uploads')->url($record->payment_proof) . '" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-md border border-gray-100 mx-auto" /></div>'
                                        ))
                                ])
                        ),
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
