<?php

namespace App\Filament\Imports;

use App\Models\Product;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'max:255'])
                ->requiredMapping(),

            ImportColumn::make('description')
                ->rules(['nullable'])
                ->requiredMapping(),

            ImportColumn::make('price')
                ->rules(['required', 'numeric', 'min:0'])
                ->requiredMapping(),

            ImportColumn::make('stock')
                ->rules(['required', 'integer', 'min:0'])
                ->requiredMapping(),

            ImportColumn::make('category_id')
                ->rules(['required', 'exists:categories,id'])
                ->requiredMapping(),

            ImportColumn::make('depot_id')
                ->rules(['required', 'exists:depots,id'])
                ->requiredMapping(),

            ImportColumn::make('is_active')
                ->rules(['nullable', 'boolean'])
        ];
    }

    public function resolveRecord(): ?Product
    {
        try {
            Log::info('Importing product data:', $this->data);

            // Try to find existing product by name
            $product = Product::where('name', $this->data['name'])->first();

            if (!$product) {
                $product = new Product();
            }

            // Map the data
            $product->fill([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'price' => $this->data['price'],
                'stock' => $this->data['stock'],
                'category_id' => $this->data['category_id'],
                'depot_id' => $this->data['depot_id'],
                'is_active' => $this->data['is_active'] ?? true,
            ]);

            // Save the product
            if ($product->save()) {
                Log::info('Product saved successfully:', ['id' => $product->id, 'name' => $product->name]);
            } else {
                Log::error('Failed to save product:', ['name' => $this->data['name']]);
            }

            return $product;
        } catch (\Exception $e) {
            Log::error('Error in ProductImporter:', [
                'message' => $e->getMessage(),
                'data' => $this->data
            ]);
            throw $e;
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
