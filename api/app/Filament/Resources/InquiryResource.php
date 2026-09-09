<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\InquiryResource\Pages\ViewInquiry;
use App\Models\Inquiry;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Pesan Masuk';

    protected static ?string $pluralModelLabel = 'Pesan Masuk (Form Kontak)';

    protected static ?string $modelLabel = 'Pesan Masuk';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('full_name')
                ->label('Nama Lengkap')
                ->disabled(),
            TextInput::make('email')
                ->label('Email')
                ->disabled(),
            TextInput::make('phone')
                ->label('Nomor Telepon')
                ->disabled(),
            TextInput::make('company')
                ->label('Perusahaan / Organisasi')
                ->disabled(),
            TextInput::make('service_interest')
                ->label('Layanan Diminati')
                ->disabled(),
            Select::make('status')
                ->label('Status Tindak Lanjut')
                ->options([
                    'unread' => 'Belum Dibaca',
                    'read' => 'Sudah Dibaca',
                    'replied' => 'Sudah Ditindaklanjuti',
                ])
                ->required(),
            Textarea::make('message')
                ->label('Pesan Pengunjung')
                ->disabled()
                ->rows(5)
                ->columnSpan(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('service_interest')
                    ->label('Layanan')
                    ->badge(),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Ditindaklanjuti',
                    ]),
                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Ditindaklanjuti',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view' => ViewInquiry::route('/{record}'),
        ];
    }
}
