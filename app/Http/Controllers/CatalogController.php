<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CatalogController extends Controller
{
    public function show(Product $product)
    {
        $recommended = Product::where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        return view('catalog.show', compact('product', 'recommended'));
    }
}
