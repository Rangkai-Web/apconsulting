<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Faq;
use App\Models\Inquiry;
use App\Models\Team;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $unreadInquiries = Inquiry::where('status', 'unread')->count();
        $publishedArticles = Article::where('is_published', true)->count();
        $activeFaqs = Faq::where('is_active', true)->count();
        $activeTeamMembers = Team::where('is_active', true)->count();

        return [
            Stat::make('Pesan Masuk (Unread)', $unreadInquiries)
                ->description($unreadInquiries > 0 ? 'Perlu segera ditanggapi' : 'Tidak ada pesan baru')
                ->descriptionIcon('heroicon-m-envelope')
                ->color($unreadInquiries > 0 ? 'warning' : 'success'),

            Stat::make('Artikel Terbit', $publishedArticles)
                ->description('Artikel & insight publik')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('FAQ Aktif', $activeFaqs)
                ->description('Pertanyaan aktif di website')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray'),

            Stat::make('Tim Profesional', $activeTeamMembers)
                ->description('Anggota tim terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
