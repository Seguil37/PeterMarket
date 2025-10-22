<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // 1) Validación de datos del formulario
        $data = $request->validate([
            'customer_name'    => ['required','string','max:120'],
            'customer_email'   => ['required','email','max:150'],
            'customer_address' => ['nullable','string','max:200'],
            'payment_method'   => ['required','in:simulated,cash,card'],
        ]);

        // 2) Leer carrito
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart'=>'Tu carrito está vacío.']);
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $tax = round($subtotal * 0.18, 2);
        $total = round($subtotal + $tax, 2);

        // 3) Transacción: validar stock, crear orden, descontar stock
        $order = DB::transaction(function () use ($cart, $data, $subtotal, $tax, $total, $request) {

            // a) Validar stock con bloqueo optimista
            foreach ($cart as $item) {
                $p = Product::lockForUpdate()->find($item['id']);
                if (!$p || $item['quantity'] > $p->stock) {
                    abort(422, 'Stock insuficiente para: '.$item['name']);
                }
            }

            // b) Crear orden
            $order = Order::create([
                'user_id'         => optional($request->user())->id,
                'customer_name'   => $data['customer_name'],
                'customer_email'  => $data['customer_email'],
                'customer_address'=> $data['customer_address'] ?? '',
                'payment_method'  => $data['payment_method'],
                'status'          => 'paid',            // pago simulado
                'subtotal'        => $subtotal,
                'tax'             => $tax,
                'total'           => $total,
                'payment_ref'     => 'SIM-'.str()->upper(str()->random(8)),
            ]);

            // c) Crear items y descontar stock
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['id'],
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);

                Product::where('id',$item['id'])->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        // 4) Limpiar carrito y redirigir a éxito
        session()->forget('cart');

        return redirect()->route('order.success', $order);
    }

    public function success(Order $order)
    {
        $order->load('items');
        return view('checkout.success', compact('order'));
    }
}
