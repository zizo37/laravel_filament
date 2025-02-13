<?php

namespace App\Filament\Resources\OrdesrResource\Pages;

use App\Filament\Resources\OrdesrResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrdesr extends EditRecord
{
    protected static string $resource = OrdesrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
