<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

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
            'contact_email' => $settings['contact_email'] ?? 'info@pcgmitra.co.id',
            'contact_phone' => $settings['contact_phone'] ?? '+62 21 1234 5678',
            'whatsapp_number' => $settings['whatsapp_number'] ?? '6281234567890',
            'office_address' => $settings['office_address'] ?? '',
            'google_maps_embed_url' => $settings['google_maps_embed_url'] ?? '',
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
            ->schema([
                Section::make('Identitas Perusahaan')
                    ->description('Upload logo perusahaan yang akan ditampilkan di header/footer website.')
                    ->schema([
                        FileUpload::make('company_logo')
                            ->label('Logo Perusahaan')
                            ->disk('public_uploads')
                            ->directory('settings')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'])
                            ->helperText('Upload logo resmi perusahaan. Ukuran maks: 2MB. Format: JPG, PNG, WEBP, SVG.'),
                    ]),

                Section::make('Informasi Kontak & Lokasi')
                    ->description('Pengaturan email, nomor telepon, WhatsApp, alamat kantor, dan lokasi peta.')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email Kontak')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('contact_phone')
                            ->label('Nomor Telepon Kantor')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('whatsapp_number')
                            ->label('Nomor WhatsApp (Form Kontak)')
                            ->tel()
                            ->required()
                            ->placeholder('6281234567890')
                            ->helperText('Format nomor WhatsApp (misal: 6281234567890) yang akan menerima pesan dari form kontak.'),
                        Textarea::make('office_address')
                            ->label('Alamat Kantor')
                            ->rows(3)
                            ->columnSpan(2)
                            ->maxLength(500),
                        Textarea::make('google_maps_embed_url')
                            ->label('Google Maps Embed URL / iFrame src')
                            ->rows(3)
                            ->columnSpan(2)
                            ->helperText('Paste URL Google Maps Embed (https://www.google.com/maps/embed?...) untuk peta lokasi kantor.'),
                    ])
                    ->columns(2),

                Section::make('Media Sosial Perusahaan')
                    ->description('Tautan ke akun media sosial resmi perusahaan.')
                    ->schema([
                        TextInput::make('social_linkedin')
                            ->label('LinkedIn URL')
                            ->url()
                            ->placeholder('https://linkedin.com/company/...')
                            ->maxLength(255),
                        TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->url()
                            ->placeholder('https://instagram.com/...')
                            ->maxLength(255),
                        TextInput::make('social_tiktok')
                            ->label('TikTok URL')
                            ->url()
                            ->placeholder('https://tiktok.com/@...')
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
                        TextInput::make('social_twitter')
                            ->label('Twitter / X URL')
                            ->url()
                            ->placeholder('https://twitter.com/...')
                            ->maxLength(255),
                        TextInput::make('social_youtube')
                            ->label('YouTube URL')
                            ->url()
                            ->placeholder('https://youtube.com/@...')
                            ->maxLength(255),
                    ])
                    ->columns(2),
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
            Setting::setKey($key, $value, $group);
        }

        Notification::make()
            ->title('Pengaturan perusahaan berhasil disimpan')
            ->success()
            ->send();
    }
}
