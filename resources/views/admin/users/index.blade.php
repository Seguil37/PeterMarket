@extends('layouts.admin')
@section('admin-content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Administradores</h1>
    <p class="text-sm text-gray-500">Gestiona los usuarios con acceso al panel.</p>
  </div>
  <a href="{{ route('admin.admins.create') }}" class="bg-black text-white px-4 py-2 rounded">Nuevo admin</a>
</div>

@if(session('ok'))
  <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('ok') }}</div>
@endif
@if($errors->has('general'))
  <div class="mb-4 rounded bg-red-100 p-3 text-red-700">{{ $errors->first('general') }}</div>
@endif

<div class="overflow-x-auto bg-white border rounded">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="p-3 text-left">Nombre</th>
        <th class="p-3 text-left">Correo</th>
        <th class="p-3 text-left">Rol</th>
        <th class="p-3 text-left">Estado</th>
        <th class="p-3 text-right">Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($admins as $admin)
        <tr class="border-t">
          <td class="p-3">{{ $admin->name }}</td>
          <td class="p-3">{{ $admin->email }}</td>
          <td class="p-3">
            @if($admin->is_master_admin)
              <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full bg-purple-100 text-purple-800">Admin Master</span>
            @else
              <span class="inline-flex items-center text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700">Admin Usuario</span>
            @endif
          </td>
          <td class="p-3">
            @if($admin->is_active)
              <span class="text-green-600 font-medium">Activo</span>
            @else
              <span class="text-red-600 font-medium">Inactivo</span>
            @endif
          </td>
          <td class="p-3 text-right space-x-2">
            <a href="{{ route('admin.admins.edit', $admin) }}" class="px-3 py-1 border rounded">Editar</a>
            <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este administrador?')">
              @csrf
              @method('DELETE')
              <button class="px-3 py-1 border rounded" @disabled(auth()->id() === $admin->id)>Eliminar</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="p-6 text-center text-gray-500">Aún no hay administradores configurados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $admins->links() }}</div>
@endsection
