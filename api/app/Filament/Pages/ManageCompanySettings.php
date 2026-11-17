<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ManageCompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Pengaturan Perusahaan';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.pages.manage-company-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        $this->form->fill([
            'company_logo' => $settings['company_logo'] ?? null,
            'contact_email' => $settings['contact_email'] ?? 'contact@apconsulting.id',
            'contact_phone' => $settings['contact_phone'] ?? '+62 857 5417 2217',
            'whatsapp_number' => $settings['whatsapp_number'] ?? '6285754172217',
            'office_address' => $settings['office_address'] ?? '',
            'google_maps_embed_url' => $settings['google_maps_embed_url'] ?? '',
            'branch_office_address' => $settings['branch_office_address'] ?? '',
            'branch_google_maps_embed_url' => $settings['branch_google_maps_embed_url'] ?? '',
            'social_linkedin' => $settings['social_linkedin'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_tiktok' => $settings['social_tiktok'] ?? '',
            'social_threads' => $settings['social_threads'] ?? '',
            'social_facebook' => $settings['social_facebook'] ?? '',
            'social_twitter' => $settings['social_twitter'] ?? '',
            'social_youtube' => $settings['social_youtube'] ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                // ==========================================
                // KOLOM KIRI (Kontak & Lokasi - 2 Kolom Lebar)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Informasi Kontak & WhatsApp Kantor')
                            ->description('Email dan nomor kontak kantor untuk layanan klien')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->label('Email Resmi Kantor')
                                    ->email()
                                    ->placeholder('info@apconsulting.id')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('contact_phone')
                                    ->label('Nomor Telepon Kantor (Hunting / PSTN)')
                                    ->tel()
                                    ->placeholder('+62 21 1234 5678')
                                    ->maxLength(255),

                                TextInput::make('whatsapp_number')
                                    ->label('Nomor WhatsApp Resmi (Tujuan Form Kontak)')
                                    ->tel()
                                    ->required()
                                    ->placeholder('6281234567890')
                                    ->helperText('Gunakan format angka tanpa tanda plus/spasi (contoh: 6281234567890). Nomor ini menerima pesan konsultasi saat klien mengisi formulir kontak di website.')
                                    ->columnSpan(2),
                            ])
                            ->columns(2),

                        Section::make('Alamat Kantor & Lokasi Peta (Google Maps)')
                            ->description('Lokasi fisik kantor untuk footer & halaman Kontak')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Textarea::make('office_address')
                                    ->label('Alamat Kantor Pusat')
                                    ->placeholder('Contoh: SUN Plaza, Jl. Meruya Ilir Raya No. 20, Jakarta Barat')
                                    ->rows(3)
                                    ->maxLength(500),

                                Textarea::make('google_maps_embed_url')
                                    ->label('Google Maps Embed URL — Kantor Pusat')
                                    ->rows(3)
                                    ->placeholder('https://www.google.com/maps/embed?pb=...')
                                    ->helperText('Tempelkan URL embed dari Google Maps (https://www.google.com/maps/embed?...) untuk peta interaktif kantor pusat.'),

                                Textarea::make('branch_office_address')
                                    ->label('Alamat Kantor Cabang (Opsional)')
                                    ->placeholder('Contoh: Ruko Alexandrite 3 No. 23, Jl. Boulevard Gading Serpong, Tangerang')
                                    ->rows(3)
                                    ->maxLength(500),

                                Textarea::make('branch_google_maps_embed_url')
                                    ->label('Google Maps Embed URL — Kantor Cabang (Opsional)')
                                    ->rows(3)
                                    ->placeholder('https://www.google.com/maps/embed?pb=...')
                                    ->helperText('Tempelkan URL embed dari Google Maps untuk peta interaktif kantor cabang.'),
                            ]),

                        Section::make('Media Sosial Perusahaan')
                            ->description('Tautan akun profil publik perusahaan')
                            ->icon('heroicon-o-share')
                            ->collapsible()
                            ->schema([
                                TextInput::make('social_linkedin')
                                    ->label('LinkedIn Perusahaan')
                                    ->url()
                                    ->placeholder('https://linkedin.com/company/...')
                                    ->maxLength(255)
                                    ->helperText('Sangat direkomendasikan untuk kredibilitas kantor konsultan pajak & bisnis.')
                                    ->columnSpan(2),

                                TextInput::make('social_instagram')
                                    ->label('Instagram URL')
                                    ->url()
                                    ->placeholder('https://instagram.com/...')
                                    ->maxLength(255),

                                TextInput::make('social_twitter')
                                    ->label('Twitter / X URL')
                                    ->url()
                                    ->placeholder('https://x.com/...')
                                    ->maxLength(255),

                                TextInput::make('social_threads')
                                    ->label('Threads URL')
                                    ->url()
                                    ->placeholder('https://threads.net/@...')
                                    ->maxLength(255),

                                TextInput::make('social_facebook')
                                    ->label('Facebook URL')
                                    ->url()
                                    ->placeholder('https://facebook.com/...')
                                    ->maxLength(255),

                                TextInput::make('social_tiktok')
                                    ->label('TikTok URL')
                                    ->url()
                                    ->placeholder('https://tiktok.com/@...')
                                    ->maxLength(255),

                                TextInput::make('social_youtube')
                                    ->label('YouTube URL')
                                    ->url()
                                    ->placeholder('https://youtube.com/@...')
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),

                // ==========================================
                // KOLOM KANAN (Logo & Legalitas - 1 Kolom)
                // ==========================================
                Group::make()
                    ->schema([
                        Section::make('Logo Resmi Perusahaan')
                            ->description('Logo utama untuk header & footer website')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                FileUpload::make('company_logo')
                                    ->label('Unggah Logo')
                                    ->disk('public_uploads')
                                    ->directory('settings')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                                    ->helperText('Format: PNG, JPG, WEBP, SVG. Maksimal: 2MB. Disarankan dengan latar belakang transparan.'),
                            ]),

                        Section::make('Legalitas & Kredensial')
                            ->description('Data resmi kantor konsultan')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Placeholder::make('license_info')
                                    ->hiddenLabel()
                                    ->content(new HtmlString('
                                        <div style="font-size: 0.82rem; line-height: 1.6; color: #94a3b8;">
                                            <strong>Izin Kemenkeu RI:</strong><br/>
                                            <span style="color: #ffffff; font-weight: 600;">KIP-6592/IP.B/PJ/2020</span><br/><br/>
                                            <strong>Keanggotaan Asosiasi:</strong><br/>
                                            <span style="color: #ffffff; font-weight: 600;">IKPI (Ikatan Konsultan Pajak Indonesia)</span><br/><br/>
                                            <strong>Legalitas Terdaftar:</strong><br/>
                                            Kantor konsultan pajak berbasis hukum resmi di Republik Indonesia.
                                        </div>
                                    ')),
                            ]),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 1,
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            $group = str_starts_with($key, 'social_') ? 'social' : 'contact';
            if ($key === 'company_logo') {
                $group = 'company';
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                ]
            );
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
