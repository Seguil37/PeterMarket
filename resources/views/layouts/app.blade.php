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
  <header class="relative">
    <div class="bg-gradient-to-r from-[#0f172a] via-[#0b5394] to-[#0ea5e9] text-white">
      <div class="section-shell py-2 flex flex-wrap items-center justify-center gap-3 text-sm">
        <span class="meta-chip text-white/90 bg-white/10 border-white/20">🚚 Envíos rápidos a todo el país</span>
        <span class="meta-chip text-white/90 bg-white/10 border-white/20">💳 Pagos seguros y protegidos</span>
        <span class="meta-chip text-white/90 bg-white/10 border-white/20">🛒 Atención personalizada 24/7</span>
      </div>
    </div>

    <div class="sticky top-0 z-40 backdrop-blur-xl bg-white/85 border-b border-white/60 shadow-sm">
      <div class="section-shell py-4 flex items-center justify-between gap-6">
        <div class="flex items-center gap-3">
          <span class="brand-badge">
            <span class="inline-flex size-8 items-center justify-center rounded-full bg-white/20">PM</span>
            Peter Market
          </span>
          <span class="hidden lg:inline-flex text-sm text-gray-600">Compras ágiles, frescas y seguras</span>
        </div>

        <nav class="hidden md:flex items-center gap-2">
          <a href="{{ route('catalog.index') }}" class="nav-pill {{ request()->routeIs('catalog.*') ? 'bg-blue-50 text-blue-700' : '' }}">Productos</a>
          <a href="{{ route('about') }}" class="nav-pill {{ request()->routeIs('about') ? 'bg-blue-50 text-blue-700' : '' }}">Nosotros</a>
          @auth
            <a href="{{ route('account.dashboard') }}" class="nav-pill {{ request()->routeIs('account.*') ? 'bg-blue-50 text-blue-700' : '' }}">Mi cuenta</a>
            @if(auth()->user()->is_admin)
              <a href="{{ route('admin.dashboard') }}" class="nav-pill {{ request()->routeIs('admin.*') ? 'bg-blue-50 text-blue-700' : '' }}">Admin</a>
            @endif
          @endauth
        </nav>

        <div class="flex items-center gap-3">
          @guest
            <a href="{{ route('login') }}" class="btn-outline hidden sm:inline-flex">Iniciar sesión</a>
            <a href="{{ route('admin.login') }}" class="btn-soft hidden sm:inline-flex">Acceso admin</a>
          @else
            <form method="POST" action="{{ route('logout') }}" class="hidden sm:block">
              @csrf
              <button type="submit" class="btn-outline">Salir</button>
            </form>
          @endguest

          <a href="{{ route('cart.index') }}" class="relative inline-flex items-center justify-center rounded-xl bg-blue-50 text-blue-700 px-3 py-2 hover:bg-blue-100 transition" aria-label="Carrito">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
              <path d="M8.25 21a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0Zm10 0a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0Z" />
              <path fill-rule="evenodd" d="M2.25 3.5a.75.75 0 0 1 .75-.75h1.86a1 1 0 0 1 .97.757l.3 1.243h12.37a1 1 0 0 1 .97 1.243l-1.25 5a1 1 0 0 1-.97.757H7.48l.246 1h10.026a.75.75 0 0 1 0 1.5H7.05a1 1 0 0 1-.97-.757L4.55 4.25H3a.75.75 0 0 1-.75-.75Zm4.887 9h9.36l.9-3.6H6.862l.275 1.1a1 1 0 0 0 .97.757Z" clip-rule="evenodd" />
            </svg>
            @if(($cartCount ?? 0) > 0)
              <span class="absolute -top-2 -right-2 min-w-6 h-6 px-1 rounded-full bg-blue-600 text-white text-xs font-semibold flex items-center justify-center shadow-sm">{{ $cartCount }}</span>
            @endif
          </a>
        </div>
      </div>
    </div>
  </header>

  {{-- CONTENIDO --}}
  <main class="flex-1">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="mt-16 bg-gradient-to-r from-[#0f172a] via-[#0b5394] to-[#0ea5e9] text-white">
    <div class="section-shell py-12 grid gap-10 lg:gap-14 lg:grid-cols-[1.2fr,1fr,1fr]">
      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <span class="brand-badge">
            <span class="inline-flex size-8 items-center justify-center rounded-full bg-white/20">PM</span>
            Peter Market
          </span>
          <span class="badge-pill bg-white/15 text-white">Desde {{ date('Y') }}</span>
        </div>
        <p class="leading-relaxed text-blue-50">Supermercado moderno con compras ágiles, seguras y listas para tu día a día. Entregamos frescura y confianza en cada pedido.</p>
        <div class="flex flex-wrap gap-3 text-sm text-blue-50">
          <span class="meta-chip text-white/90 bg-white/10 border-white/20">Soporte 24/7</span>
          <span class="meta-chip text-white/90 bg-white/10 border-white/20">Garantía de satisfacción</span>
        </div>
      </div>

      <div class="space-y-4">
        <div class="text-lg font-semibold">Enlaces rápidos</div>
        <ul class="space-y-2 text-sm">
          <li><a class="hover:underline text-blue-50" href="{{ route('catalog.index') }}">Productos</a></li>
          <li><a class="hover:underline text-blue-50" href="{{ route('about') }}">Nosotros</a></li>
          <li><a class="hover:underline text-blue-50" href="{{ route('cart.index') }}">Carrito</a></li>
        </ul>
      </div>

      <div class="space-y-4">
        <div class="text-lg font-semibold">Recibe novedades</div>
        <p class="text-sm text-blue-50">Suscríbete para recibir promociones exclusivas, lanzamientos de temporada y consejos frescos para tu compra.</p>
        <form class="flex flex-col sm:flex-row gap-3">
          <label class="sr-only" for="newsletter-email">Correo electrónico</label>
          <input id="newsletter-email" type="email" placeholder="tu@email.com" class="flex-1 rounded-xl px-4 py-3 border border-white/30 bg-white/15 text-white placeholder:text-blue-100 focus:outline-none focus:ring-2 focus:ring-white/50" />
          <button type="button" class="btn-gradient">Suscribirme</button>
        </form>
        <p class="text-xs text-blue-100">&copy; {{ date('Y') }} Peter Market • Todos los derechos reservados</p>
      </div>
    </div>
  </footer>

  {{-- scripts por-vista --}}
  @stack('scripts')
</body>
</html>
