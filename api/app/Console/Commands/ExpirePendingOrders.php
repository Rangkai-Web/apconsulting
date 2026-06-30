<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Expire pending orders that have passed the Snap token expiry window and release their reserved stock.';

    public function handle(): void
    {
        $threshold = now()->subHours((int) config('midtrans.order_expire_hours'));

        $orders = Order::where('status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->with('items.variant')
            ->get();

        foreach ($orders as $order) {
            $order->status = 'expired';
            $order->save();
            $order->releaseStockIfApplicable('pending', 'expired');
        }

        Log::info("orders:expire-pending: expired {$orders->count()} orders.");
    }
}
