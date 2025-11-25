@extends('layouts.app')
@section('title','Mi cuenta')

@section('content')
  <div class="max-w-5xl mx-auto px-4 py-10 space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <p class="text-sm text-gray-500">Panel del cliente</p>
        <h1 class="text-3xl font-bold text-gray-900">Hola, {{ $user->name }}</h1>
        <p class="text-gray-600">{{ $user->email }}</p>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('catalog.index') }}" class="btn-ghost text-blue-700 hover:text-blue-800">Seguir comprando</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn-ghost text-red-600 hover:text-red-700">Cerrar sesión</button>
        </form>
      </div>
    </div>

    @if(session('status'))
      <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
        {{ session('status') }}
      </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
      {{-- Historial de compras --}} 
      <div class="lg:col-span-2 card-surface">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
          <div>
            <p class="text-xs uppercase tracking-wide text-gray-500">Compras</p>
            <h2 class="text-lg font-semibold">Historial de pedidos</h2>
          </div>
          <span class="text-sm text-gray-500">{{ $orders->total() }} orden{{ $orders->total() === 1 ? '' : 'es' }}</span>
        </div>

        @if($orders->count() === 0)
          <div class="p-6 text-center text-gray-600">
            <p class="font-semibold">Aún no tienes compras.</p>
            <p class="text-sm text-gray-500">Explora nuestros productos y realiza tu primera orden.</p>
          </div>
        @else
          <div class="divide-y divide-slate-100">
            @foreach($orders as $order)
              <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                  <p class="text-sm text-gray-500">Orden #{{ $order->id }} • {{ $order->created_at->format('d/m/Y H:i') }}</p>
                  <p class="font-semibold text-gray-900">Total: S/ {{ number_format($order->total, 2) }}</p>
                  <p class="text-sm text-gray-600">{{ $order->items->sum('quantity') }} artículo{{ $order->items->sum('quantity') === 1 ? '' : 's' }} • Pago: {{ $order->payment_method }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 uppercase">{{ $order->status }}</span>
                </div>
              </div>
            @endforeach
          </div>

          <div class="px-5 py-4 border-t border-slate-100">{{ $orders->links() }}</div>
        @endif
      </div>

      {{-- Seguridad de la cuenta --}} 
      <div class="card-surface p-5 space-y-4">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500">Seguridad</p>
          <h2 class="text-lg font-semibold">Cambiar contraseña</h2>
          <p class="text-sm text-gray-600">Actualiza tu contraseña para mantener segura tu cuenta.</p>
        </div>

        <form method="POST" action="{{ route('account.password.update') }}" class="space-y-4">
          @csrf
          @method('PUT')

          <div>
            <label class="block text-sm font-medium text-gray-700">Contraseña actual</label>
            <input type="password" name="current_password" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('current_password')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
            <input type="password" name="password" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('password')
              <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
          </div>

          <button type="submit" class="w-full px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Actualizar contraseña</button>
        </form>

        <div class="pt-2 border-t">
          <p class="text-sm text-gray-600">¿Necesitas ayuda? Escríbenos a soporte@petermarket.local</p>
        </div>
      </div>
    </div>
  </div>
@endsection
