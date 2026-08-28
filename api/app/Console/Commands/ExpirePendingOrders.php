<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending';

    protected $description = 'Expire pending orders whose payment was never completed or verified, and release their reserved stock.';

    public function handle(): void
    {
        $threshold = now()->subHours((int) config('orders.expire_hours'));

        $orders = Order::where('order_status', 'pending')
            ->where('payment_status', '!=', 'awaiting_verification')
            ->where('created_at', '<=', $threshold)
            ->with('items.variant')
            ->get();

        foreach ($orders as $order) {
            $order->order_status = 'expired';
            $order->save();
            $order->releaseStockIfApplicable('pending', 'expired');
        }

        Log::info("orders:expire-pending: expired {$orders->count()} orders.");
    }
}
