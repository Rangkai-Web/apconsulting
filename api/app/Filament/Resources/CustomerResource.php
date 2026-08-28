<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\ViewCustomer;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->withCount('orders')
                ->withSum(['orders as orders_total_spent' => fn (Builder $q) => $q->whereIn('order_status', ['paid', 'processing', 'shipped', 'completed'])], 'total'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('-'),
                TextColumn::make('orders_count')
                    ->label('Jumlah Pesanan')
                    ->counts('orders')
                    ->sortable(),
                TextColumn::make('orders_total_spent')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Bergabung')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Pelanggan')
                ->schema([
                    TextEntry::make('name')->label('Nama'),
                    TextEntry::make('email')->label('Email'),
                    TextEntry::make('phone')->label('Telepon')->placeholder('-'),
                    TextEntry::make('created_at')->label('Bergabung')->dateTime('d M Y H:i'),
                    TextEntry::make('orders_count')->label('Jumlah Pesanan')->state(fn (User $record): int => $record->orders()->count()),
                    TextEntry::make('orders_total_spent')
                        ->label('Total Belanja')
                        ->state(fn (User $record): int => (int) $record->orders()->whereIn('order_status', ['paid', 'processing', 'shipped', 'completed'])->sum('total'))
                        ->money('IDR'),
                ])
                ->columns(3),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'view' => ViewCustomer::route('/{record}'),
        ];
    }
}
