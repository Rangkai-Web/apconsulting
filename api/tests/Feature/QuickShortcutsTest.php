<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageCompanySettings;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\FaqResource;
use App\Filament\Resources\TeamResource;
use App\Filament\Widgets\QuickShortcutsWidget;
use Livewire\Livewire;
use Tests\TestCase;

class QuickShortcutsTest extends TestCase
{
    public function test_quick_shortcuts_widget_contains_valid_resource_urls(): void
    {
        Livewire::test(QuickShortcutsWidget::class)
            ->assertSee(ArticleResource::getUrl('create'), false)
            ->assertSee(FaqResource::getUrl('create'), false)
            ->assertSee(TeamResource::getUrl('create'), false)
            ->assertSee(ManageCompanySettings::getUrl(), false)
            ->assertDontSee('/admin/teams/create', false)
            ->assertDontSee('/admin/articles/create', false)
            ->assertDontSee('/admin/faqs/create', false)
            ->assertDontSee('/admin/manage-company-settings', false);
    }
}
