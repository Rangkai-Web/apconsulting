<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $revenue = Order::whereIn('order_status', ['paid', 'processing', 'shipped', 'completed'])->sum('total');
        $newOrdersCount = Order::whereIn('order_status', ['pending', 'paid'])->count();
        $productsCount = Product::count();
        $customersCount = User::count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description('Total transaksi berhasil')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('success'),
            Stat::make('Pesanan Baru', $newOrdersCount)
                ->description('Perlu segera diproses')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($newOrdersCount > 0 ? 'warning' : 'gray'),
            Stat::make('Total Produk', number_format($productsCount, 0, ',', '.'))
                ->description('Produk aktif terdaftar')
                ->descriptionIcon('heroicon-m-cube'),
            Stat::make('Total Pelanggan', number_format($customersCount, 0, ',', '.'))
                ->description('Pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-users'),
        ];
    }
}
