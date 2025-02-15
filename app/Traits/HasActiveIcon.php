<?php

namespace App\Traits;

trait HasActiveIcon
{
    public static function getActiveNavigationIcon(): ?string
    {
        return str(static::getNavigationIcon())
            ->replace('heroicon-o-', 'heroicon-s-')
            ->toString();
    }
}
