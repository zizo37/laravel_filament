<?php

namespace App\Filament\Resources\OrdesrResource\Pages;

use App\Filament\Resources\OrdesrResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdesrs extends ListRecords
{
    protected static string $resource = OrdesrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
