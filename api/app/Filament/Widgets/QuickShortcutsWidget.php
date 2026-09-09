<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class QuickShortcutsWidget extends Widget
{
    protected string $view = 'filament.widgets.quick-shortcuts-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;
}
