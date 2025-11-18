<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title','Peter Market')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-dvh flex flex-col">

  {{-- HEADER --}}
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
      <a href="{{ route('catalog.index') }}" class="text-xl font-bold text-blue-700">Peter Market</a>

      <nav class="flex items-center gap-6">
        {{-- Productos --}}
        <a href="{{ route('catalog.index') }}"
           class="{{ request()->routeIs('catalog.*') ? 'text-blue-700 font-semibold' : 'text-gray-700 hover:text-gray-900' }}">
          Productos
        </a>

        {{-- Nosotros --}}
        <a href="{{ route('about') }}"
           class="{{ request()->routeIs('about') ? 'text-blue-700 font-semibold' : 'text-gray-700 hover:text-gray-900' }}">
          Nosotros
        </a>

        {{-- MODO ADMIN / ENTRAR / SALIR (PASO 7) --}}
        @auth
          <div class="flex items-center gap-4">
            <div class="text-right leading-tight">
              <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
              <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            @if(auth()->user()->is_admin)
              <a href="{{ route('admin.dashboard') }}"
                 class="{{ request()->routeIs('admin.*') ? 'text-blue-700 font-semibold' : 'text-gray-700 hover:text-gray-900' }}">
                Admin
              </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="inline">
              @csrf
              <button type="submit"
                      class="text-gray-700 hover:text-gray-900">
                Salir
              </button>
            </form>
          </div>
        @else
          <div class="flex items-center gap-4">
            <a href="{{ route('login') }}"
               class="{{ request()->routeIs('login') ? 'text-blue-700 font-semibold' : 'text-gray-700 hover:text-gray-900' }}">
              Entrar
            </a>
            <a href="{{ route('admin.login') }}" class="text-sm text-gray-500 hover:text-gray-700">
              Acceso admin
            </a>
          </div>
        @endauth

        {{-- Carrito --}}
        <a href="{{ route('cart.index') }}"
           class="relative px-3 py-1 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
          Carrito
          @if(($cartCount ?? 0) > 0)
            <span class="absolute -top-2 -right-2 min-w-6 h-6 px-1 rounded-full bg-emerald-500 text-white text-xs flex items-center justify-center">
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
  <footer class="mt-8 bg-white border-t">
    <div class="max-w-7xl mx-auto px-4 py-6 grid gap-4 sm:grid-cols-3 text-sm text-gray-600">
      <div>
        <div class="font-semibold text-gray-900">Peter Market</div>
        <p>Supermercado moderno y accesible.</p>
      </div>
      <div>
        <div class="font-semibold text-gray-900">Enlaces</div>
        <ul class="space-y-1">
          <li><a class="hover:underline" href="{{ route('catalog.index') }}">Productos</a></li>
          <li><a class="hover:underline" href="{{ route('about') }}">Nosotros</a></li>
          <li><a class="hover:underline" href="{{ route('cart.index') }}">Carrito</a></li>
        </ul>
      </div>
      <div>
        <div class="font-semibold text-gray-900">Soporte</div>
        <p>soporte@petermarket.local</p>
        <p class="mt-2">&copy; {{ date('Y') }} Peter Market • Todos los derechos reservados</p>
      </div>
    </div>
  </footer>

  {{-- scripts por-vista --}}
  @stack('scripts')
</body>
</html>
