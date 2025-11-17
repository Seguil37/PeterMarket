@extends('layouts.app')
@section('title','Acceso administrativo')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
  <div class="w-full max-w-sm border rounded-xl p-6">
    <h1 class="text-xl font-semibold mb-4">Iniciar sesión (Admin)</h1>

    @if ($errors->any())
      <div class="mb-4 rounded bg-red-100 text-red-800 p-3">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.post') }}" class="grid gap-4">
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
        <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
      </div>

      {{-- RECORDAR --}}
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="remember" class="h-4 w-4"> Recordarme
      </label>

      {{-- BOTÓN --}}
      <button class="bg-blue-600 text-white rounded px-4 py-2">Entrar al panel</button>
    </form>

    <p class="mt-3 text-center text-sm text-gray-600">
      ¿Eres cliente? <a href="{{ route('login') }}" class="text-blue-600">Ir al login de clientes</a>
    </p>
  </div>
</div>
@endsection
