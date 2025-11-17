@extends('layouts.app')
@section('title','Administradores del sistema')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Administradores</h1>
      <p class="text-sm text-gray-500">Gestiona las cuentas operativas y el Admin Master.</p>
    </div>
    <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
      + Nuevo admin
    </a>
  </div>

  @if (session('status'))
    <div class="mb-4 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3">{{ session('status') }}</div>
  @endif

  @if ($errors->has('delete'))
    <div class="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ $errors->first('delete') }}</div>
  @endif

  <div class="overflow-x-auto bg-white border rounded-xl">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
          <th class="px-4 py-3">Nombre</th>
          <th class="px-4 py-3">Correo</th>
          <th class="px-4 py-3">Rol</th>
          <th class="px-4 py-3">Estado</th>
          <th class="px-4 py-3 text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($admins as $admin)
          <tr class="border-t">
            <td class="px-4 py-3 font-medium text-gray-900">{{ $admin->name }}</td>
            <td class="px-4 py-3 text-gray-600">{{ $admin->email }}</td>
            <td class="px-4 py-3">
              @if($admin->is_master_admin)
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-purple-100 text-purple-700">Admin Master</span>
              @else
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-700">Admin Usuario</span>
              @endif
            </td>
            <td class="px-4 py-3">
              @if($admin->is_active)
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-emerald-100 text-emerald-700">Activo</span>
              @else
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-700">Suspendido</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex gap-2">
                <a href="{{ route('admin.admins.edit', $admin) }}" class="text-blue-600 hover:underline">Editar</a>
                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('¿Eliminar esta cuenta de admin?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-600 hover:underline">Eliminar</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Aún no hay administradores registrados.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $admins->links() }}
  </div>
</div>
@endsection
