<?php

namespace App\Filament\Pages;

use App\Models\RajaongkirDistrict;
use App\Models\StoreSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Pengaturan Toko';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.settings-page';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = StoreSetting::firstOrCreate([], [
            'store_name' => 'Doctor Plant Indonesia',
        ]);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('store_name')
                    ->label('Nama Toko')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo')
                    ->label('Logo Toko')
                    ->disk('public_uploads')
                    ->directory('settings')
                    ->image()
                    ->maxSize(2048)
                    ->helperText('Format: JPG, PNG, WEBP. Maksimal 2MB.'),
                Textarea::make('address')
                    ->label('Alamat Toko')
                    ->rows(3),
                TextInput::make('phone')
                    ->label('Nomor Telepon CS')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('whatsapp_number')
                    ->label('Nomor WhatsApp')
                    ->placeholder('628123456789')
                    ->tel()
                    ->maxLength(255)
                    ->helperText('Gunakan format kode negara tanpa tanda +, misal: 628123456789'),
                TextInput::make('instagram_url')
                    ->label('Instagram URL')
                    ->url()
                    ->maxLength(255),
                TextInput::make('operating_hours')
                    ->label('Jam Operasional')
                    ->placeholder('Senin - Minggu, 10.00 - 22.00 WIB')
                    ->maxLength(255),
                Select::make('rajaongkir_origin_district_id')
                    ->label('Kecamatan Asal (RajaOngkir)')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => RajaongkirDistrict::with('city.province')
                        ->where('name', 'like', "%{$search}%")
                        ->limit(50)
                        ->get()
                        ->mapWithKeys(fn (RajaongkirDistrict $district) => [
                            $district->id => "{$district->name}, {$district->city?->name}, {$district->city?->province?->name}",
                        ])
                        ->toArray())
                    ->getOptionLabelUsing(function ($value): ?string {
                        $district = RajaongkirDistrict::with('city.province')->find($value);

                        return $district ? "{$district->name}, {$district->city?->name}, {$district->city?->province?->name}" : null;
                    })
                    ->placeholder('Pilih kecamatan asal pengiriman')
                    ->required()
                    ->helperText('Wajib diisi supaya fitur cek ongkir di storefront berfungsi. Pastikan kota/provinsi yang tertera sesuai — beberapa kecamatan punya nama sama di daerah berbeda.'),
                RichEditor::make('about_content')
                    ->label('Konten Halaman Tentang Kami')
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'strike',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'attachFiles',
                        'redo',
                        'undo',
                    ])
                    ->fileAttachmentsDisk('public_uploads')
                    ->fileAttachmentsDirectory('settings/about')
                    ->fileAttachmentsVisibility('public')
                    ->helperText('Tampil di halaman /about storefront. Kosongkan untuk pakai teks bawaan.'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $settings = StoreSetting::first();
        if ($settings) {
            $settings->update($this->form->getState());
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
