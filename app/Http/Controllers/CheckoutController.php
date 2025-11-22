<?php

namespace App\Http\Controllers;

use App\Mail\CompraRealizadaMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // 1) Validación de datos del formulario
        $data = $request->validate([
            'customer_name'     => ['required','string','max:120'],
            'customer_email'    => ['required','email','max:150'],
            'delivery_type'     => ['required','in:delivery,pickup'],
            'shipping_address'  => ['nullable','string','max:200','required_if:delivery_type,delivery'],
            'shipping_city'     => ['nullable','string','max:120','required_if:delivery_type,delivery'],
            'shipping_reference'=> ['nullable','string','max:200','required_if:delivery_type,delivery'],
            'shipping_type'     => ['nullable', Rule::in(array_keys(Delivery::options())), 'required_if:delivery_type,delivery'],
            'payment_method'    => ['required','in:simulated,cash,card'],
        ]);

        // 2) Leer carrito
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->withErrors(['cart'=>'Tu carrito está vacío.']);
        }

        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $deliveryEvaluation = $data['delivery_type'] === 'delivery'
            ? Delivery::evaluate($subtotal)
            : ['available' => true, 'cost' => 0, 'message' => 'Recojo en tienda sin costo de envío'];
        if ($data['delivery_type'] === 'delivery' && !$deliveryEvaluation['available']) {
            return back()->withErrors(['shipping_type' => $deliveryEvaluation['message']])->withInput();
        }

        $shippingOptions = Delivery::options($deliveryEvaluation['cost']);
        $shippingCost = $data['delivery_type'] === 'delivery' ? $deliveryEvaluation['cost'] : 0;
        $tax = round($subtotal * 0.18, 2);
        $total = round($subtotal + $tax + $shippingCost, 2);

        // 3) Transacción: validar stock, crear orden, descontar stock
        $order = DB::transaction(function () use ($cart, $data, $subtotal, $tax, $total, $request, $shippingCost) {

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
                'customer_address'=> $data['shipping_address'], // compatibilidad con campo antiguo
                'shipping_address'=> $data['delivery_type'] === 'delivery' ? $data['shipping_address'] : '',
                'shipping_city'   => $data['delivery_type'] === 'delivery' ? $data['shipping_city'] : '',
                'shipping_reference' => $data['delivery_type'] === 'delivery' ? $data['shipping_reference'] : '',
                'shipping_type'   => $data['delivery_type'] === 'delivery' ? $data['shipping_type'] : 'pickup',
                'delivery_type'   => $data['delivery_type'],
                'shipping_cost'   => $shippingCost,
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
                    'category_type' => $item['category_type'] ?? 'General',
                    'name'       => $item['name'],
                    'price'      => $item['price'],
                    'quantity'   => $item['quantity'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);

                Product::where('id',$item['id'])->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        $order->load('items');
        Mail::to($order->customer_email)->send(new CompraRealizadaMail($order));

        // 4) Limpiar carrito y redirigir a éxito
        session()->forget('cart');

        return redirect()->route('order.success', $order);
    }

    public function success(Order $order)
    {
        $order->load('items');
        $shippingOptions = Delivery::options($order->shipping_cost);
        $deliveryEvaluation = $order->delivery_type === 'delivery'
            ? Delivery::evaluate($order->subtotal)
            : ['available' => true, 'cost' => 0, 'message' => 'Recojo en tienda sin costo de envío'];

        return view('checkout.success', compact('order','shippingOptions','deliveryEvaluation'));
    }
}
