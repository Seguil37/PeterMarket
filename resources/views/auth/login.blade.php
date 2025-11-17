@extends('layouts.app')
@section('title','Iniciar sesión')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
  <div class="w-full max-w-sm border rounded-xl p-6">
    <h1 class="text-xl font-semibold mb-4">Iniciar sesión</h1>

    @if ($errors->any())
      <div class="mb-4 rounded bg-red-100 text-red-800 p-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="grid gap-4">
      @csrf
      {{-- CORREO --}}
      <div>
        <label class="block text-sm mb-1">Correo</label>
        <input type="email" name="email" value="{{ old('email') }}" 
               class="w-full border rounded px-3 py-2" required autofocus>
      </div>

      {{-- CONTRASEÑA --}}
      <div>
        <label class="block text-sm mb-1">Contraseña</label>
        <div class="relative">
          <input type="password" name="password" id="password"
                 class="w-full border rounded px-3 py-2 pr-10" required>
          <button type="button" id="togglePassword"
                  class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-600">
            <!-- Ojo cerrado (por defecto) -->
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" 
                 class="h-5 w-5 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10 
                       0-1.093.176-2.142.5-3.125m3.29 1.44A5.977 5.977 0 0012 7c3.314 0 
                       6 2.686 6 6 0 1.015-.252 1.972-.7 2.805M15 12a3 3 0 11-6 0 
                       3 3 0 016 0z" />
            </svg>
            <!-- Ojo abierto (se muestra al activar) -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" 
                 class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 
                       8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 
                       7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
      </div>

      {{-- RECORDAR --}}
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="remember" class="h-4 w-4"> Recuérdame
      </label>

      {{-- BOTÓN --}}
      <button class="bg-black text-white rounded px-4 py-2">Entrar</button>
    </form>

    <p class="mt-3 text-center">
      ¿No tienes cuenta? <a href="{{ route('register') }}" class="text-blue-600">Regístrate como cliente</a>
    </p>
    <p class="mt-1 text-center text-sm text-gray-600">
      ¿Eres administrador? <a href="{{ route('admin.login') }}" class="text-blue-600">Ir al login de admins</a>
    </p>
  </div>
</div>

{{-- SCRIPT PARA OJITO --}}
<script>
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const eyeOpen = document.getElementById('eyeOpen');
  const eyeClosed = document.getElementById('eyeClosed');

  togglePassword.addEventListener('click', () => {
    const isPassword = passwordInput.getAttribute('type') === 'password';
    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
    eyeOpen.classList.toggle('hidden', !isPassword);
    eyeClosed.classList.toggle('hidden', isPassword);
  });
</script>
@endsection
