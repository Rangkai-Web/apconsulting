<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Update Status')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options(OrderResource::statusOptions())
                        ->default(fn (Order $record): string => $record->status)
                        ->live()
                        ->required(),
                    TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->visible(fn (Get $get): bool => $get('status') === 'shipped')
                        ->required(fn (Get $get): bool => $get('status') === 'shipped'),
                ])
                ->action(function (array $data, Order $record): void {
                    $oldStatus = $record->status;
                    $record->status = $data['status'];

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
