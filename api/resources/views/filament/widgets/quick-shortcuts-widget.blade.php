<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #ffffff;">
                <!-- <span style="display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 4px; background: rgba(8, 105, 178, 0.25); color: #38bdf8;">
                    ⚡
                </span> -->
                <span>Aksi Cepat Admin (Quick Shortcuts)</span>
            </div>
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1rem; margin-top: 0.25rem;">
            <!-- Shortcut 1: Tulis Artikel Baru -->
            <a href="{{ \App\Filament\Resources\ArticleResource::getUrl('create') }}" 
               style="display: flex; align-items: center; gap: 0.85rem; padding: 1.1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.09); background-color: rgba(255, 255, 255, 0.03); text-decoration: none; color: inherit; transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='#0869B2'; this.style.backgroundColor='rgba(8, 105, 178, 0.12)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.09)'; this.style.backgroundColor='rgba(255, 255, 255, 0.03)'; this.style.transform='translateY(0)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background-color: rgba(8, 105, 178, 0.2); color: #38bdf8; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-pencil-square" style="width: 22px; height: 22px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #ffffff; line-height: 1.25;">Tulis Artikel Baru</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 3px;">Publikasi insight & regulasi</div>
                </div>
            </a>

            <!-- Shortcut 2: Tambah FAQ -->
            <a href="{{ \App\Filament\Resources\FaqResource::getUrl('create') }}" 
               style="display: flex; align-items: center; gap: 0.85rem; padding: 1.1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.09); background-color: rgba(255, 255, 255, 0.03); text-decoration: none; color: inherit; transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='#f59e0b'; this.style.backgroundColor='rgba(245, 158, 11, 0.12)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.09)'; this.style.backgroundColor='rgba(255, 255, 255, 0.03)'; this.style.transform='translateY(0)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background-color: rgba(245, 158, 11, 0.2); color: #fbbf24; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-question-mark-circle" style="width: 22px; height: 22px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #ffffff; line-height: 1.25;">Tambah FAQ</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 3px;">Pertanyaan umum klien</div>
                </div>
            </a>

            <!-- Shortcut 3: Tambah Tim -->
            <a href="{{ \App\Filament\Resources\TeamResource::getUrl('create') }}" 
               style="display: flex; align-items: center; gap: 0.85rem; padding: 1.1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.09); background-color: rgba(255, 255, 255, 0.03); text-decoration: none; color: inherit; transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='#06b6d4'; this.style.backgroundColor='rgba(6, 182, 212, 0.12)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.09)'; this.style.backgroundColor='rgba(255, 255, 255, 0.03)'; this.style.transform='translateY(0)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background-color: rgba(6, 182, 212, 0.2); color: #22d3ee; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-user-plus" style="width: 22px; height: 22px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #ffffff; line-height: 1.25;">Tambah Anggota Tim</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 3px;">Profil tim konsultan</div>
                </div>
            </a>

            <!-- Shortcut 4: Edit Pengaturan -->
            <a href="{{ \App\Filament\Pages\ManageCompanySettings::getUrl() }}" 
               style="display: flex; align-items: center; gap: 0.85rem; padding: 1.1rem; border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.09); background-color: rgba(255, 255, 255, 0.03); text-decoration: none; color: inherit; transition: all 0.2s ease;"
               onmouseover="this.style.borderColor='#10b981'; this.style.backgroundColor='rgba(16, 185, 129, 0.12)'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.borderColor='rgba(255, 255, 255, 0.09)'; this.style.backgroundColor='rgba(255, 255, 255, 0.03)'; this.style.transform='translateY(0)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 8px; background-color: rgba(16, 185, 129, 0.2); color: #34d399; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" style="width: 22px; height: 22px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 700; color: #ffffff; line-height: 1.25;">Pengaturan Kantor</div>
                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 3px;">Kontak, logo, &amp; WhatsApp</div>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
