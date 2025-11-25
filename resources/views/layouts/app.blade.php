<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Peter Market')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-dvh flex flex-col">

  {{-- HEADER --}}
  <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-6">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-bold shadow-lg">PM</span>
        <div>
          <a href="{{ route('catalog.index') }}" class="text-xl font-bold text-gray-900">Peter Market</a>
          <p class="text-xs text-gray-500">Supermercado inteligente y confiable</p>
        </div>
      </div>

      <nav class="flex items-center gap-4">
        {{-- Productos --}}
        <a href="{{ route('catalog.index') }}"
           class="pill-nav {{ request()->routeIs('catalog.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
          Productos
        </a>

        {{-- Nosotros --}}
        <a href="{{ route('about') }}"
           class="pill-nav {{ request()->routeIs('about') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
          Nosotros
        </a>

        {{-- MODO ADMIN / ENTRAR / SALIR (PASO 7) --}}
        @auth
          <div class="flex items-center gap-3">
            <div class="text-right leading-tight">
              <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
              <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <a href="{{ route('account.dashboard') }}"
               class="pill-nav {{ request()->routeIs('account.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
              Mi cuenta
            </a>

            @if(auth()->user()->is_admin)
              <a href="{{ route('admin.dashboard') }}"
                 class="pill-nav {{ request()->routeIs('admin.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
                Admin
              </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button type="submit"
                      class="pill-nav bg-white text-gray-700 border border-gray-200 hover:border-gray-400">
                Salir
              </button>
            </form>
          </div>
        @else
          <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="pill-nav {{ request()->routeIs('login') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-400' }}">
              Entrar
            </a>
            <a href="{{ route('admin.login') }}" class="pill-nav bg-white text-gray-500 border border-gray-200 hover:border-gray-400">
              Acceso admin
            </a>
          </div>
        @endauth

        {{-- Carrito --}}
        <a href="{{ route('cart.index') }}"
           class="relative pill-nav bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg">
          Carrito
          @if(($cartCount ?? 0) > 0)
            <span class="absolute -top-2 -right-2 min-w-6 h-6 px-1 rounded-full bg-white text-emerald-600 text-xs font-semibold flex items-center justify-center shadow">
              {{ $cartCount }}
            </span>
          @endif
        </a>
      </nav>
    </div>
  </header>

  {{-- CONTENIDO --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="mt-12 bg-white/80 backdrop-blur border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 py-10 grid gap-6 sm:grid-cols-3 text-sm text-gray-600">
      <div class="space-y-2">
        <div class="font-semibold text-gray-900 text-base">Peter Market</div>
        <p class="leading-relaxed">Supermercado moderno con compras ágiles, seguras y listas para tu día a día.</p>
      </div>
      <div>
        <div class="font-semibold text-gray-900 text-base mb-2">Enlaces</div>
        <ul class="space-y-2">
          <li><a class="hover:underline" href="{{ route('catalog.index') }}">Productos</a></li>
          <li><a class="hover:underline" href="{{ route('about') }}">Nosotros</a></li>
          <li><a class="hover:underline" href="{{ route('cart.index') }}">Carrito</a></li>
        </ul>
      </div>
      <div class="space-y-2">
        <div class="font-semibold text-gray-900 text-base">Soporte</div>
        <p>soporte@petermarket.local</p>
        <p class="pt-2">&copy; {{ date('Y') }} Peter Market • Todos los derechos reservados</p>
      </div>
    </div>
  </footer>

  {{-- scripts por-vista --}}
  @stack('scripts')
</body>
</html>
