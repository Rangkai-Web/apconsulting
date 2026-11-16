<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\InquiryResource\Pages\ViewInquiry;
use App\Models\Inquiry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Pesan Masuk';

    protected static ?string $pluralModelLabel = 'Pesan Masuk (Form Kontak)';

    protected static ?string $modelLabel = 'Pesan Masuk';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Detail Pesan - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Isi Pesan Konsultasi')
                            ->description('Pertanyaan & kebutuhan yang diajukan oleh calon klien')
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->schema([
                                Textarea::make('message')
                                    ->label('Pesan Lengkap Pengunjung')
                                    ->disabled()
                                    ->rows(7)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Identitas Calon Klien')
                            ->description('Profil pengirim pesan dari website')
                            ->icon('heroicon-o-user')
                            ->schema([
                                TextInput::make('full_name')
                                    ->label('Nama Lengkap')
                                    ->disabled(),

                                TextInput::make('company')
                                    ->label('Perusahaan / Organisasi')
                                    ->placeholder('Tidak dicantumkan')
                                    ->disabled(),

                                TextInput::make('email')
                                    ->label('Alamat Email')
                                    ->disabled(),

                                TextInput::make('phone')
                                    ->label('Nomor WhatsApp / Telepon')
                                    ->disabled(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                // ==========================================
                // KOLOM KANAN (Status & Metadata - 1 Kolom)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Status Tindak Lanjut')
                            ->description('Kelola progres penanganan pesan')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Select::make('status')
                                    ->label('Status Penanganan')
                                    ->options([
                                        'unread' => '🔴 Belum Dibaca (Unread)',
                                        'read' => '🔵 Sudah Dibaca (Read)',
                                        'replied' => '🟢 Sudah Ditindaklanjuti (Replied)',
                                    ])
                                    ->required(),

                                TextInput::make('service_interest')
                                    ->label('Layanan yang Diminati')
                                    ->disabled(),

                                Placeholder::make('created_at')
                                    ->label('Tanggal Pesan Masuk')
                                    ->content(fn (?Inquiry $record): string => $record ? $record->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-'),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('full_name')
                    ->label('Pengirim & Perusahaan')
                    ->searchable(['full_name', 'company', 'email'])
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Inquiry $record): string => $record->company ?: 'Individu / Personal')
                    ->wrap(),

                TextColumn::make('message')
                    ->label('Isi Pesan Konsultasi')
                    ->searchable()
                    ->limit(65)
                    ->tooltip(fn (Inquiry $record): string => $record->message)
                    ->wrap(),

                TextColumn::make('service_interest')
                    ->label('Layanan')
                    ->badge()
                    ->color('info')
                    ->placeholder('Konsultasi Umum')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->url(fn (Inquiry $record): string => 'mailto:' . $record->email)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Telepon / WA')
                    ->icon('heroicon-m-phone')
                    ->searchable(),

                SelectColumn::make('status')
                    ->label('Status Tindak Lanjut')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Ditindaklanjuti',
                    ])
                    ->sortable(),
            ])
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
                Action::make('chat_wa')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function (Inquiry $record): ?string {
                        if (empty($record->phone)) {
                            return null;
                        }
                        $clean = preg_replace('/[^0-9]/', '', $record->phone);
                        if (str_starts_with($clean, '0')) {
                            $clean = '62' . substr($clean, 1);
                        }
                        $clientName = rawurlencode($record->full_name);
                        $message = rawurlencode("Halo {$record->full_name}, terima kasih telah menghubungi AP Consulting. Terkait pertanyaan konsultasi Anda melalui website...");
                        return "https://wa.me/{$clean}?text={$message}";
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Inquiry $record): bool => !empty($record->phone)),

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
