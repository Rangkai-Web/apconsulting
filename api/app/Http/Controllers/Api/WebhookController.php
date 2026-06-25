<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Order;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function midtrans(Request $request)
    {
        $serverKey = config('midtrans.server_key');

        // Signature validation
        $signatureKey = $request->input('signature_key');
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');

        // If local/testing without keys, bypass signature verification or mock it
        if (! empty($serverKey)) {
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans Webhook: Invalid signature.');
                return response()->json(['message' => 'Invalid signature.'], 403);
            }
        }

        // Find order
        $order = Order::with('items.variant')
            ->where('midtrans_order_id', $orderId)
            ->orWhere('order_number', $orderId)
            // Support checking without attempt suffix if midtrans sent raw order number
            ->orWhere('order_number', explode('-', $orderId)[0])
            ->first();

        if (! $order) {
            Log::warning("Midtrans Webhook: Order not found for ID {$orderId}");
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $transactionStatus = $request->input('transaction_status');
        $fraudStatus = $request->input('fraud_status');

        $newStatus = null;

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $newStatus = 'pending';
            } elseif ($fraudStatus === 'accept') {
                $newStatus = 'paid';
            }
        } elseif ($transactionStatus === 'settlement') {
            $newStatus = 'paid';
        } elseif ($transactionStatus === 'pending') {
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $newStatus = ($transactionStatus === 'expire') ? 'expired' : 'cancelled';
        }

        if (! $newStatus) {
            return response()->json(['message' => 'No status change needed.'], 200);
        }

        // Idempotency: skip if order is already processed to prevent stock bugs or status regression
        if (in_array($order->status, ['paid', 'processing', 'shipped', 'completed'])) {
            if (in_array($newStatus, ['cancelled', 'expired'])) {
                Log::info("Midtrans Webhook: Attempted to cancel/expire already paid order {$order->order_number}. Ignored.");
                return response()->json(['message' => 'Order already paid, cannot cancel.'], 200);
            }
        }

        // If transitioning to paid/processing, set midtrans fields
        if ($newStatus === 'paid') {
            $order->midtrans_transaction_id = $request->input('transaction_id');
        }

        $oldStatus = $order->status;
        $order->status = $newStatus;
        $order->save();

        // Release stocks back if order becomes cancelled or expired
        if (in_array($newStatus, ['cancelled', 'expired']) && $oldStatus === 'pending') {
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->qty);
                }
            }
            Log::info("Midtrans Webhook: Released stock for cancelled/expired order {$order->order_number}.");
        }

        // Filament notifications to Admin on successful payment
        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            try {
                $admins = Admin::all();
                if ($admins->count() > 0 && class_exists(Notification::class)) {
                    Notification::make()
                        ->title('Pesanan Baru Dibayar')
                        ->body("Pesanan {$order->order_number} sebesar Rp " . number_format($order->total) . " telah berhasil dibayar.")
                        ->success()
                        ->sendToDatabase($admins);
                }
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim database notification ke Filament admin: ' . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Webhook processed successfully.',
            'order_number' => $order->order_number,
            'status' => $newStatus,
        ], 200);
    }
}
