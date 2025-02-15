<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
            ->label('Code'),
            ExportColumn::make('name')
            ->label('Name'),
            ExportColumn::make('description')
            ->label('Description'),
            ExportColumn::make('price')
            ->label('Price'),
            ExportColumn::make('stock')
            ->label('Stock'),
            ExportColumn::make('category.name')
            ->label('Category'),
            ExportColumn::make('depot.name')
            ->label('Depot'),
            ExportColumn::make('is_active')
            ->label('Active'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your product export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
