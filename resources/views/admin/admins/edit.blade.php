@extends('layouts.app')

@section('title', 'Editar administrador')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10 space-y-6">
  <div>
    <a href="{{ route('admin.admins.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver al listado</a>
    <h1 class="text-2xl font-semibold mt-2">Editar cuenta de administrador</h1>
    <p class="text-sm text-gray-600">Actualiza los datos o el rol del administrador seleccionado.</p>
  </div>

  @if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-900">
      <ul class="list-disc pl-5 space-y-1 text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white border rounded-2xl p-6">
    <form method="POST" action="{{ route('admin.admins.update', $adminUser) }}" class="space-y-5">
      @csrf
      @method('PUT')
      <div>
        <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
        <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Correo</label>
        <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Rol</label>
        <select name="admin_role" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
          <option value="{{ \App\Models\User::ROLE_MASTER }}" @selected(old('admin_role', $adminUser->admin_role) === \App\Models\User::ROLE_MASTER)>Admin Master</option>
          <option value="{{ \App\Models\User::ROLE_OPERATOR }}" @selected(old('admin_role', $adminUser->admin_role) === \App\Models\User::ROLE_OPERATOR)>Admin Usuario</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Contraseña (opcional)</label>
        <input type="password" name="password" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
        <p class="text-xs text-gray-500 mt-1">Déjalo vacío para mantener la contraseña actual.</p>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
      </div>
      <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg border text-gray-700">Cancelar</a>
        <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
@endsection
