<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('status', 'order_status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('order_status');
            $table->string('payment_proof')->nullable()->after('shipping_cost');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->string('payment_rejected_reason')->nullable()->after('payment_proof_uploaded_at');
            $table->dropColumn(['midtrans_order_id', 'midtrans_transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_proof', 'payment_proof_uploaded_at', 'payment_rejected_reason']);
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('order_status', 'status');
        });
    }
};
