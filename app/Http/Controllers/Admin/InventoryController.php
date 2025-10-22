<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q',''));

        $movs = InventoryMovement::with(['product','user'])
            ->when($q !== '', function ($qq) use ($q) {
                return $qq->whereHas('product', fn($p) => $p->where('name','like',"%{$q}%"))
                          ->orWhere('note','like',"%{$q}%");
            })
            ->latest()->paginate(15)->withQueryString();

        $products = Product::orderBy('name')->get(['id','name','stock']);

        return view('admin.inventory.index', compact('movs','products','q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'type'       => ['required','in:in,out'],
            'quantity'   => ['required','integer','min:1'],
            'unit_cost'  => ['nullable','numeric','min:0'],
            'note'       => ['nullable','string','max:1000'],
        ]);

        /** @var \App\Models\User|null $u */
        $u = $request->user();                    // ✅ evita auth()->id()
        $userId = $u ? $u->id : null;             // ✅ sin “undefined method id”

        DB::transaction(function () use ($data, $userId) {
            $mov = InventoryMovement::create($data + ['user_id' => $userId]);

            // bloquear fila del producto para actualizar stock de forma segura
            $product = Product::lockForUpdate()->findOrFail($mov->product_id);
            $product->applyInventory($mov->type, (int)$mov->quantity);
        });

        return back()->with('ok','Movimiento registrado.');
    }

    public function destroy(InventoryMovement $inventory, Request $request)
    {
        DB::transaction(function () use ($inventory) {
            $product = Product::lockForUpdate()->findOrFail($inventory->product_id);
            $product->applyInventory($inventory->type === 'in' ? 'out' : 'in', (int)$inventory->quantity);
            $inventory->delete();
        });

        return back()->with('ok','Movimiento eliminado y stock revertido.');
    }
}
