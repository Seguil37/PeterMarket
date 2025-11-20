<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Delivery;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function cart(){ return session()->get('cart', []); }
    private function saveCart($cart){ session()->put('cart', $cart); }

    public function index()
    {
        $cart = $this->cart();
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $iva = round($subtotal * 0.18, 2);
        $shippingOptions = Delivery::options();
        $shippingType = session()->getOldInput('shipping_type', array_key_first($shippingOptions));
        $shippingCost = $shippingOptions[$shippingType]['cost'] ?? ($shippingOptions[array_key_first($shippingOptions)]['cost'] ?? 0);
        $total = round($subtotal + $iva + $shippingCost, 2);

        return view('cart.index', compact('cart','subtotal','iva','total','shippingOptions','shippingType','shippingCost'));
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'quantity'   => ['nullable','integer','min:1'],
        ]);

        $product = Product::select('id','name','price','image_url','stock','category_type')->findOrFail($data['product_id']);
        $qty = max(1, (int)($data['quantity'] ?? 1));

        if ($qty > $product->stock) {
            return back()->withErrors(['quantity' => 'Cantidad supera el stock disponible.']);
        }

        $cart = $this->cart();
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = min($cart[$product->id]['quantity'] + $qty, $product->stock);
        } else {
            $cart[$product->id] = [
                'id'=>$product->id,
                'name'=>$product->name,
                'price'=>(float)$product->price,
                'image'=>$product->image_url,
                'quantity'=>$qty,
                'stock'=>$product->stock,
                'category_type'=>$product->category_type,
            ];
        }
        $this->saveCart($cart);
        return redirect()->route('cart.index')->with('status','Producto agregado al carrito.');
    }

    public function update(Request $request, int $productId)
    {
        $data = $request->validate(['quantity'=>['required','integer','min:0']]);
        $cart = $this->cart();
        if(!isset($cart[$productId])) return response()->json(['message'=>'No existe en el carrito'],404);

        if ($data['quantity'] === 0) {
            unset($cart[$productId]);
        } else {
            $stock = $cart[$productId]['stock'] ?? Product::find($productId)?->stock ?? 0;
            $cart[$productId]['quantity'] = min($data['quantity'], $stock);
        }
        $this->saveCart($cart);
        return $request->wantsJson() ? response()->json(['ok'=>true]) : redirect()->route('cart.index');
    }

    public function remove(int $productId)
    {
        $cart = $this->cart();
        unset($cart[$productId]);
        $this->saveCart($cart);
        return redirect()->route('cart.index')->with('status','Producto eliminado.');
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('status','Carrito vaciado.');
    }
}
