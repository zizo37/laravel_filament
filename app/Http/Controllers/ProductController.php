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
        ->active()
        ->get()
        ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => asset('storage/' . $product->image),
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'category' => $product->category ? $product->category->name : 'No Category',
                'depot' => $product->depot ? $product->depot->name : 'No Depot',
                'is_active' => $product->is_active
            ];
        });

    return Inertia::render('Products', ['products' => $products]);
}

public function show($id)
{
    $product = Product::with(['category', 'depot'])->findOrFail($id);
    $productData = [
        'id' => $product->id,
        'name' => $product->name,
        'image' => asset('storage/' . $product->image),
        'description' => $product->description,
        'price' => $product->price,
        'stock' => $product->stock,
        'category' => $product->category ? $product->category->name : 'No Category',
        'depot' => $product->depot ? $product->depot->name : 'No Depot',
        'is_active' => $product->is_active
    ];

    // Fetch related products (e.g., same category)
    $relatedProducts = Product::where('category_id', $product->category_id)
        ->where('id', '!=', $id)
        ->active()
        ->take(6)
        ->get()
        ->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'image' => asset('storage/' . $p->image),
                'description' => $p->description,
                'price' => $p->price,
                'stock' => $p->stock,
                'category' => $p->category ? $p->category->name : 'No Category',
                'depot' => $p->depot ? $p->depot->name : 'No Depot',
            ];
        });

    return Inertia::render('ProductDetails', [
        'product' => $productData,
        'relatedProducts' => $relatedProducts
    ]);
}

}
