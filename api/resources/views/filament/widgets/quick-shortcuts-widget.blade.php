<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚡ Aksi Cepat Admin (Quick Shortcuts)
        </x-slot>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
            <!-- Shortcut 1: Tulis Artikel Baru -->
            <a href="{{ url('/admin/articles/create') }}" 
               style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background-color: rgba(156, 163, 175, 0.05); text-decoration: none; color: inherit; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#3b82f6'; this.style.backgroundColor='rgba(59,130,246,0.1)'"
               onmouseout="this.style.borderColor='rgba(156, 163, 175, 0.25)'; this.style.backgroundColor='rgba(156, 163, 175, 0.05)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 0.5rem; background-color: rgba(59,130,246,0.18); color: #3b82f6; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-pencil-square" style="width: 24px; height: 24px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; line-height: 1.25;">Tulis Artikel Baru</div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;">Publikasi wawasan baru</div>
                </div>
            </a>

            <!-- Shortcut 2: Tambah FAQ -->
            <a href="{{ url('/admin/faqs/create') }}" 
               style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background-color: rgba(156, 163, 175, 0.05); text-decoration: none; color: inherit; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#f59e0b'; this.style.backgroundColor='rgba(245,158,11,0.1)'"
               onmouseout="this.style.borderColor='rgba(156, 163, 175, 0.25)'; this.style.backgroundColor='rgba(156, 163, 175, 0.05)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 0.5rem; background-color: rgba(245,158,11,0.18); color: #f59e0b; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-question-mark-circle" style="width: 24px; height: 24px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; line-height: 1.25;">Tambah FAQ</div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;">Tambah info pertanyaan</div>
                </div>
            </a>

            <!-- Shortcut 3: Tambah Tim -->
            <a href="{{ url('/admin/teams/create') }}" 
               style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background-color: rgba(156, 163, 175, 0.05); text-decoration: none; color: inherit; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#06b6d4'; this.style.backgroundColor='rgba(6,182,212,0.1)'"
               onmouseout="this.style.borderColor='rgba(156, 163, 175, 0.25)'; this.style.backgroundColor='rgba(156, 163, 175, 0.05)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 0.5rem; background-color: rgba(6,182,212,0.18); color: #06b6d4; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-user-plus" style="width: 24px; height: 24px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; line-height: 1.25;">Tambah Anggota Tim</div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;">Profil tim profesional</div>
                </div>
            </a>

            <!-- Shortcut 4: Edit Pengaturan -->
            <a href="{{ url('/admin/manage-company-settings') }}" 
               style="display: flex; align-items: center; gap: 0.75rem; padding: 1rem; border-radius: 0.75rem; border: 1px solid rgba(156, 163, 175, 0.25); background-color: rgba(156, 163, 175, 0.05); text-decoration: none; color: inherit; transition: all 0.2s;"
               onmouseover="this.style.borderColor='#10b981'; this.style.backgroundColor='rgba(16,185,129,0.1)'"
               onmouseout="this.style.borderColor='rgba(156, 163, 175, 0.25)'; this.style.backgroundColor='rgba(156, 163, 175, 0.05)'">
                <div style="display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 0.5rem; background-color: rgba(16,185,129,0.18); color: #10b981; flex-shrink: 0;">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" style="width: 24px; height: 24px;" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; font-weight: 600; line-height: 1.25;">Pengaturan Perusahaan</div>
                    <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 2px;">Edit kontak, logo & WA</div>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
