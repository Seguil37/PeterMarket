<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query();
        if ($s = $request->input('s')) $q->where('name','like',"%{$s}%");
        $products = $q->latest('id')->paginate(10)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create() { return view('admin.products.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'image_url'   => ['nullable','url'],
        ]);
        Product::create($data);
        return redirect()->route('admin.products.index')->with('ok','Producto creado.');
    }

    public function edit(Product $product) { return view('admin.products.edit', compact('product')); }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'image_url'   => ['nullable','url'],
        ]);
        $product->update($data);
        return redirect()->route('admin.products.index')->with('ok','Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('ok','Producto eliminado.');
    }
}