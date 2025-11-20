@extends('layouts.app')
@section('title','Carrito de compras')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6" x-data="cartPage({{ json_encode($shippingOptions) }}, '{{ $shippingType }}', {{ $shippingCost }})">
  <header class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Carrito de compras</h1>
    <a href="{{ route('catalog.index') }}" class="text-blue-600 underline">Seguir comprando</a>
  </header>

  @if (session('status'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
      @foreach ($errors->all() as $error) <div>{{ $error }}</div> @endforeach
    </div>
  @endif

  @if (empty($cart))
    <div class="text-center p-10 border rounded bg-white">
      <p class="text-lg">Tu carrito está vacío.</p>
      <a href="{{ route('catalog.index') }}" class="inline-block mt-4 px-4 py-2 rounded bg-blue-600 text-white">Ir al catálogo</a>
    </div>
  @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-4">
        @foreach ($cart as $item)
          <div class="flex items-center gap-4 p-4 border rounded bg-white">
            <img src="{{ $item['image'] }}" class="w-20 h-20 object-cover rounded" alt="">
            <div class="flex-1">
              <h2 class="font-semibold">{{ $item['name'] }}</h2>
              <p class="text-sm text-gray-600">S/ {{ number_format($item['price'], 2) }}</p>
              <p class="text-xs text-gray-500">Stock: {{ $item['stock'] }}</p>
              <div class="mt-3 flex items-center gap-2">
                <button class="px-2 py-1 border rounded" @click="decrement({{ $item['id'] }})">−</button>
                <input type="number" min="0"
                       :value="quantities[{{ $item['id'] }}] ?? {{ $item['quantity'] }}"
                       @change="updateQty({{ $item['id'] }}, $event.target.value)"
                       class="w-16 text-center border rounded py-1">
                <button class="px-2 py-1 border rounded" @click="increment({{ $item['id'] }}, {{ $item['stock'] }})">+</button>
                <form method="POST" action="{{ route('cart.remove', $item['id']) }}" class="ml-4">
                  @csrf @method('DELETE')
                  <button class="text-red-600 hover:underline">Eliminar</button>
                </form>
              </div>
            </div>
            <div class="text-right">
              <div class="text-sm text-gray-600">Subtotal</div>
              <div class="text-lg font-semibold">S/ <span x-text="formatMoney({{ $item['price'] }} * (quantities[{{ $item['id'] }}] ?? {{ $item['quantity'] }}))">{{ number_format($item['price'] * $item['quantity'],2) }}</span></div>
            </div>
          </div>
        @endforeach
        <form method="POST" action="{{ route('cart.clear') }}">
          @csrf @method('DELETE')
          <button class="mt-2 text-sm text-gray-600 hover:text-red-600">Vaciar carrito</button>
        </form>
      </div>

      <aside class="border rounded p-4 h-fit bg-white">
        <h3 class="text-lg font-semibold mb-3">Resumen</h3>
        <dl class="space-y-2">
          <div class="flex justify-between"><dt>Subtotal</dt><dd>S/ <span x-text="formatMoney(totals.subtotal)">{{ number_format($subtotal,2) }}</span></dd></div>
          <div class="flex justify-between"><dt>IGV (18%)</dt><dd>S/ <span x-text="formatMoney(totals.iva)">{{ number_format($iva,2) }}</span></dd></div>
          <div class="flex justify-between"><dt>Delivery</dt><dd>S/ <span x-text="formatMoney(totals.shipping)">{{ number_format($shippingCost,2) }}</span></dd></div>
          <div class="flex justify-between font-bold text-lg pt-2 border-t"><dt>Total</dt><dd>S/ <span x-text="formatMoney(totals.total)">{{ number_format($total,2) }}</span></dd></div>
        </dl>

        {{-- Formulario de pago --}}
        <form method="POST" action="{{ route('checkout.process') }}" class="mt-4 space-y-3">
          @csrf
          <div>
            <label class="text-sm block mb-1">Nombre y apellido</label>
            <input name="customer_name" value="{{ old('customer_name') }}" required class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="text-sm block mb-1">Correo</label>
            <input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="w-full border rounded px-3 py-2">
          </div>
          <div class="space-y-3">
            <div>
              <label class="text-sm block mb-1">Dirección completa</label>
              <input name="shipping_address" value="{{ old('shipping_address') }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="text-sm block mb-1">Ciudad / distrito</label>
              <input name="shipping_city" value="{{ old('shipping_city') }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
              <label class="text-sm block mb-1">Referencia de domicilio</label>
              <input name="shipping_reference" value="{{ old('shipping_reference') }}" required class="w-full border rounded px-3 py-2">
            </div>
          </div>
          <div>
            <label class="text-sm block mb-1">Tipo de envío</label>
            <select name="shipping_type" @change="changeShipping($event.target.value)" class="w-full border rounded px-3 py-2">
              @foreach($shippingOptions as $key => $option)
                <option value="{{ $key }}" @selected(old('shipping_type', $shippingType) === $key) data-cost="{{ $option['cost'] }}">
                  {{ $option['label'] }} - S/ {{ number_format($option['cost'],2) }}
                </option>
              @endforeach
            </select>
          </div>
          <fieldset>
            <legend class="text-sm mb-1">Método de pago</legend>
            <label class="flex items-center gap-2">
              <input type="radio" name="payment_method" value="simulated" checked>
              <span>Pago simulado (pruebas)</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="payment_method" value="cash">
              <span>Efectivo / Contraentrega</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="payment_method" value="card" disabled>
              <span>Tarjeta (pronto)</span>
            </label>
          </fieldset>
          <button class="w-full px-4 py-2 rounded bg-emerald-600 text-white font-semibold">
            Pagar ahora
          </button>
          <p class="text-xs text-gray-500 text-center">Compra segura • datos validados • stock garantizado</p>
        </form>
      </aside>
    </div>
  @endif
</div>
@endsection

@push('scripts')
  {{-- Alpine para el comportamiento del carrito --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
  function cartPage(shippingOptions, initialType, shippingCost) {
    return {
      shippingOptions,
      shippingType: initialType,
      quantities: {},
      totals: { subtotal: {{ $subtotal }}, iva: {{ $iva }}, shipping: shippingCost, total: {{ $total }} },
      formatMoney(v){ return Number(v).toFixed(2); },
      increment(id, stock){ const c = Number(this.quantities[id] ?? 1); this.updateQty(id, Math.min(c+1, stock)); },
      decrement(id){ const c = Number(this.quantities[id] ?? 1); this.updateQty(id, Math.max(c-1, 0)); },
      changeShipping(type){
        this.shippingType = type;
        const option = this.shippingOptions?.[type];
        this.totals.shipping = Number(option?.cost ?? 0);
        this.totals.total = +((this.totals.subtotal + this.totals.iva + this.totals.shipping).toFixed(2));
      },
      updateQty(id, qty){
        qty = Number(qty); this.quantities = { ...this.quantities, [id]: qty };
        fetch(`/cart/${id}`, {
          method: 'PATCH',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},
          body: JSON.stringify({ quantity: qty })
        }).then(() => {
          let subtotal = 0;
          document.querySelectorAll('.lg\\:col-span-2 .border.rounded.bg-white').forEach(row=>{
            const priceText = row.querySelector('p.text-sm').textContent.replace(/[^\d.,]/g,'').replace(',','.');
            const price = Number(priceText) || 0;
            const q = Number(row.querySelector('input[type="number"]').value) || 0;
            subtotal += price * q;
          });
          this.totals.subtotal = +subtotal.toFixed(2);
          this.totals.iva = +( (subtotal*0.18).toFixed(2) );
          this.totals.total = +( (this.totals.subtotal + this.totals.iva + this.totals.shipping).toFixed(2) );
        }).catch(()=>{});
      }
    }
  }
  </script>
@endpush
