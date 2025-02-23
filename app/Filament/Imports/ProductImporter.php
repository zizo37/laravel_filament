<?php
namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductImporter extends Importer
{
    public bool $shouldSkipRows = true;

    protected static ?string $model = Product::class;

    public static function getColumns(): array
{
    return [
        ImportColumn::make('name')
            ->rules(['string', 'max:255']), // Remove required

        ImportColumn::make('description')
            ->rules(['string']), // Remove required

        ImportColumn::make('price')
            ->rules(['numeric', 'min:0']), // Remove required

        ImportColumn::make('stock')
            ->rules(['integer', 'min:0']), // Remove required

        ImportColumn::make('category_id')
            ->rules(['integer', 'exists:categories,id']), // Remove required

        ImportColumn::make('depot_id')
            ->rules(['integer', 'exists:depots,id']), // Remove required

        ImportColumn::make('is_active')
            ->rules(['boolean']),
    ];
}

public function resolveRecord(): ?Product
{
    Log::info('Processing import row', ['data' => $this->data]); // Add this

    DB::beginTransaction();

    try {
        $name = $this->data['name'] ?? null;

        if (!$name) {
            Log::error('Product name is missing in import data', ['data' => $this->data]);
            DB::rollBack();
            return null;
        }

        $product = Product::updateOrCreate(
            ['name' => $name],
            [
                'description' => $this->data['description'],
                'price' => (float) $this->data['price'],
                'stock' => (int) $this->data['stock'],
                'category_id' => (int) $this->data['category_id'],
                'depot_id' => (int) $this->data['depot_id'],
                'is_active' => (bool) ($this->data['is_active'] ?? true),
            ]
        );

        DB::commit();

        Log::info('Product imported successfully', [
            'id' => $product->id,
            'name' => $product->name
        ]);

        return $product;

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Failed to import product', [
            'error' => $e->getMessage(),
            'data' => $this->data
        ]);

        return null;
    }
}

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your product import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
