<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query();
        if ($s = $request->input('s')) $q->where('name','like',"%{$s}%");
        $products = $q->latest('id')->paginate(10)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Product::CATEGORY_TYPES;
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $this->ensureImageProvided($request);

        if ($request->hasFile('image_file')) {
            $data['image_url'] = $request->file('image_file')->store('products','public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('ok','Producto creado.');
    }

    public function edit(Product $product)
    {
        $categories = Product::CATEGORY_TYPES;
        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request);
        $this->ensureImageProvided($request, $product);

        $oldPath = $product->uploadedImagePath();
        $shouldDelete = false;

        if ($request->hasFile('image_file')) {
            $data['image_url'] = $request->file('image_file')->store('products','public');
            $shouldDelete = (bool) $oldPath;
        } elseif ($request->filled('image_url')) {
            $shouldDelete = (bool) $oldPath;
        }

        $product->update($data);

        if ($shouldDelete && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.products.index')->with('ok','Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        if ($path = $product->uploadedImagePath()) {
            Storage::disk('public')->delete($path);
        }
        $product->delete();
        return back()->with('ok','Producto eliminado.');
    }

    private function validateProduct(Request $request): array
    {
        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'category_type' => ['required','string', Rule::in(Product::CATEGORY_TYPES)],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'image_url'   => ['nullable','url'],
            'image_file'  => ['nullable','image','mimes:png','max:4096'],
        ], [
            'image_file.mimes' => 'La imagen debe estar en formato PNG.',
        ]);

        unset($data['image_file']);

        return $data;
    }

    private function ensureImageProvided(Request $request, ?Product $product = null): void
    {
        if ($request->filled('image_url') || $request->hasFile('image_file')) {
            return;
        }

        if ($product && $product->image_url) {
            return;
        }

        throw ValidationException::withMessages([
            'image_url' => 'Debes proporcionar un enlace o subir una imagen.',
        ]);
    }
}
