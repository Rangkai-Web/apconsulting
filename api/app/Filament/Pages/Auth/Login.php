<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    protected string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament-panels::components.layout.base';

    public function getTitle(): string | Htmlable
    {
        return 'Masuk Admin | AP Consulting';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Sign in to Admin Portal';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Masuk untuk mengelola artikel insight, data tim, FAQ, dan pengaturan website AP Consulting.';
    }
}
