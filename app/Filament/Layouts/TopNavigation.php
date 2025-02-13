<?php

namespace App\Filament\Layouts;

use Filament\Panel;

class TopNavigation
{
    public function __invoke(Panel $panel): Panel
    {
        return $panel
            ->topNavigation()
            ->renderHook(
                'panels::topbar.end',
                fn (): string => view('livewire.cart-dropdown')->render()
            );
    }
}
