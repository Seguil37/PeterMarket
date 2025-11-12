@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 max-w-md">
  <h1 class="text-2xl font-semibold mb-4">Registro de cliente</h1>

  @if ($errors->any())
    <div class="mb-4 text-red-600">
      <ul>
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('register.post') }}">
    @csrf

    <div class="mb-3">
      <label class="block mb-1">Nombre</label>
      <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-3 py-2" />
    </div>

    <div class="mb-3">
      <label class="block mb-1">Correo electrónico</label>
      <input type="email" name="email" value="{{ old('email') }}" required class="w-full border px-3 py-2" />
    </div>

    <div class="mb-3">
      <label class="block mb-1">Contraseña</label>
      <input type="password" name="password" required class="w-full border px-3 py-2" />
    </div>

    <div class="mb-3">
      <label class="block mb-1">Confirmar contraseña</label>
      <input type="password" name="password_confirmation" required class="w-full border px-3 py-2" />
    </div>

    <div class="flex items-center justify-between">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Registrar</button>
      <a href="{{ route('login') }}" class="text-sm text-gray-600">¿Ya tienes cuenta? Iniciar sesión</a>
    </div>
  </form>
</div>
@endsection
