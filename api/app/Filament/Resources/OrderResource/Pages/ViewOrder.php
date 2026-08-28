<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Admin;
use App\Models\Order;
use App\Notifications\OrderPaidNotification;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Log;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verifyPayment')
                ->label('Verifikasi Pembayaran')
                ->visible(fn (Order $record): bool => $record->payment_status === 'awaiting_verification')
                ->form([
                    Radio::make('decision')
                        ->label('Keputusan')
                        ->options(['accept' => 'Terima', 'reject' => 'Tolak'])
                        ->live()
                        ->required(),
                    Textarea::make('reason')
                        ->label('Alasan Penolakan')
                        ->visible(fn (Get $get): bool => $get('decision') === 'reject')
                        ->required(fn (Get $get): bool => $get('decision') === 'reject'),
                ])
                ->action(function (array $data, Order $record): void {
                    if ($data['decision'] === 'accept') {
                        $record->payment_status = 'paid';
                        if ($record->order_status === 'pending') {
                            $record->order_status = 'processing';
                        }
                        $record->save();

                        $admins = Admin::all();

                        try {
                            if ($admins->count() > 0) {
                                Notification::make()
                                    ->title('Pembayaran Diverifikasi')
                                    ->body("Pesanan {$record->order_number} sebesar Rp " . number_format($record->total) . ' telah diverifikasi dibayar.')
                                    ->success()
                                    ->sendToDatabase($admins);
                            }
                        } catch (\Throwable $e) {
                            Log::error('Gagal mengirim Filament database notification ke admin: ' . $e->getMessage());
                        }

                        try {
                            foreach ($admins as $admin) {
                                $admin->notify(new OrderPaidNotification($record));
                            }
                        } catch (\Throwable $e) {
                            Log::error('Gagal mengirim email notification ke admin: ' . $e->getMessage());
                        }
                    } else {
                        $record->payment_status = 'rejected';
                        $record->payment_rejected_reason = $data['reason'];
                        $record->save();
                    }

                    Notification::make()
                        ->title($data['decision'] === 'accept' ? 'Pembayaran diterima' : 'Pembayaran ditolak')
                        ->success()
                        ->send();
                }),
            Action::make('updateStatus')
                ->label('Update Status')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options(OrderResource::statusOptions())
                        ->default(fn (Order $record): string => $record->order_status)
                        ->live()
                        ->required()
                        ->rule(function (Get $get, Order $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($record) {
                                if ($value === 'shipped' && $record->payment_status !== 'paid') {
                                    $fail('Pesanan belum lunas — verifikasi pembayaran terlebih dahulu sebelum mengubah status ke Shipped.');
                                }
                            };
                        }),
                    TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->visible(fn (Get $get): bool => $get('status') === 'shipped')
                        ->required(fn (Get $get): bool => $get('status') === 'shipped'),
                ])
                ->action(function (array $data, Order $record): void {
                    $oldStatus = $record->order_status;
                    $record->order_status = $data['status'];

                    if ($data['status'] === 'shipped' && ! empty($data['tracking_number'])) {
                        $record->tracking_number = $data['tracking_number'];
                    }

                    if ($data['status'] === 'shipped' && $record->shipped_at === null) {
                        $record->shipped_at = now();
                    }

                    if ($data['status'] === 'completed' && $record->completed_at === null) {
                        $record->completed_at = now();
                    }

                    $record->save();
                    $record->releaseStockIfApplicable($oldStatus, $data['status']);

                    Notification::make()
                        ->title('Status pesanan diperbarui')
                        ->success()
                        ->send();
                }),
        ];
    }
}
