<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'depot'])
            ->active() // Fetch only active products
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => asset('storage/' . $product->image), // Convert image to full URL
                    'description' => $product->description,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category' => $product->category ? $product->category->name : 'No Category',
                    'depot' => $product->depot ? $product->depot->name : 'No Depot',
                    'is_active' => $product->is_active
                ];
            });

        return Inertia::render('Products', [
            'products' => $products
        ]);
    }


    public function show($id)
{
    $product = Product::findOrFail($id);
    return Inertia::render('ProductDetails', ['product' => $product]);
}


}
