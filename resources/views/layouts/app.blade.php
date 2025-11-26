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
  <header class="sticky top-0 z-40 bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between gap-6">
      <div class="flex items-center gap-3">
        <a href="{{ route('catalog.index') }}" class="text-2xl font-semibold tracking-tight text-white">Peter Market</a>
        <span class="hidden sm:inline-flex text-sm text-blue-100">Compras ágiles y seguras</span>
      </div>

      <nav class="flex items-center gap-3 md:gap-6">
        <a href="{{ route('catalog.index') }}" class="nav-link text-white/90 hover:text-white {{ request()->routeIs('catalog.*') ? 'active' : '' }}">Productos</a>
        <a href="{{ route('about') }}" class="nav-link text-white/90 hover:text-white {{ request()->routeIs('about') ? 'active' : '' }}">Nosotros</a>

        @auth
          <div class="hidden sm:flex items-center gap-4">
            <a href="{{ route('account.dashboard') }}" class="nav-link text-white/90 hover:text-white {{ request()->routeIs('account.*') ? 'active' : '' }}">Mi cuenta</a>
            @if(auth()->user()->is_admin)
              <a href="{{ route('admin.dashboard') }}" class="nav-link text-white/90 hover:text-white {{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="nav-link text-white/90 hover:text-white">Salir</button>
            </form>
          </div>
        @else
          <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="nav-link text-white/90 hover:text-white {{ request()->routeIs('login') ? 'active' : '' }}">Entrar</a>
            <a href="{{ route('admin.login') }}" class="nav-link text-white/90 hover:text-white">Acceso admin</a>
          </div>
        @endauth

        <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center text-white/90 hover:text-white transition" aria-label="Carrito">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
            <path d="M8.25 21a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0Zm10 0a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0Z" />
            <path fill-rule="evenodd" d="M2.25 3.5a.75.75 0 0 1 .75-.75h1.86a1 1 0 0 1 .97.757l.3 1.243h12.37a1 1 0 0 1 .97 1.243l-1.25 5a1 1 0 0 1-.97.757H7.48l.246 1h10.026a.75.75 0 0 1 0 1.5H7.05a1 1 0 0 1-.97-.757L4.55 4.25H3a.75.75 0 0 1-.75-.75Zm4.887 9h9.36l.9-3.6H6.862l.275 1.1a1 1 0 0 0 .97.757Z" clip-rule="evenodd" />
          </svg>
          @if(($cartCount ?? 0) > 0)
            <span class="absolute -top-2 -right-2 min-w-6 h-6 px-1 rounded-full bg-blue-600 text-white text-xs font-semibold flex items-center justify-center shadow-sm">{{ $cartCount }}</span>
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
  <footer class="mt-12 bg-[#1565c0] text-white">
    <div class="max-w-7xl mx-auto px-4 py-10 grid gap-8 sm:grid-cols-3 text-sm">
      <div class="space-y-3">
        <div class="text-lg font-semibold">Peter Market</div>
        <p class="leading-relaxed text-blue-50">Supermercado moderno con compras ágiles, seguras y listas para tu día a día.</p>
      </div>
      <div class="space-y-3">
        <div class="text-lg font-semibold">Enlaces</div>
        <ul class="space-y-2">
          <li><a class="hover:underline text-blue-50" href="{{ route('catalog.index') }}">Productos</a></li>
          <li><a class="hover:underline text-blue-50" href="{{ route('about') }}">Nosotros</a></li>
          <li><a class="hover:underline text-blue-50" href="{{ route('cart.index') }}">Carrito</a></li>
        </ul>
      </div>
      <div class="space-y-3">
        <div class="text-lg font-semibold">Soporte</div>
        <p class="text-blue-50">soporte@petermarket.local</p>
        <p class="pt-2 text-blue-50">&copy; {{ date('Y') }} Peter Market • Todos los derechos reservados</p>
      </div>
    </div>
  </footer>

  {{-- scripts por-vista --}}
  @stack('scripts')
</body>
</html>
