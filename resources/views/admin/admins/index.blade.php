@extends('layouts.app')

@section('title', 'Administrar administradores')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10 space-y-8">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl font-semibold">Control de administradores</h1>
      <p class="text-sm text-gray-600">Solo el Admin Master puede crear, editar, desactivar o eliminar cuentas de admin.</p>
    </div>
  </div>

  @if (session('success'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-900">
      <ul class="list-disc pl-5 space-y-1 text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="bg-white border rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b flex items-center justify-between">
      <h2 class="text-lg font-semibold">Listado de administradores</h2>
      <span class="text-sm text-gray-500">{{ $admins->count() }} cuentas</span>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-4 py-3 text-left">Nombre</th>
            <th class="px-4 py-3 text-left">Correo</th>
            <th class="px-4 py-3 text-left">Rol</th>
            <th class="px-4 py-3 text-left">Estado</th>
            <th class="px-4 py-3 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
          @forelse ($admins as $admin)
            <tr class="{{ $admin->is_admin ? 'bg-white' : 'bg-gray-50' }}">
              <td class="px-4 py-3 font-medium text-gray-900">{{ $admin->name }}</td>
              <td class="px-4 py-3 text-gray-600">{{ $admin->email }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $admin->isMasterAdmin() ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700' }}">
                  {{ $admin->isMasterAdmin() ? 'Admin Master' : 'Admin Usuario' }}
                </span>
              </td>
              <td class="px-4 py-3">
                @if($admin->is_admin)
                  <span class="text-emerald-700 font-semibold">Activo</span>
                @else
                  <span class="text-amber-600 font-semibold">Desactivado</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="{{ route('admin.admins.edit', $admin) }}" class="text-blue-600 hover:underline text-sm">Editar</a>

                  @if(auth()->id() !== $admin->id)
                    <form method="POST" action="{{ route('admin.admins.toggle-status', $admin) }}">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="{{ $admin->is_admin ? 'deactivate' : 'activate' }}">
                      <button type="submit" class="text-sm {{ $admin->is_admin ? 'text-amber-600' : 'text-emerald-600' }} hover:underline">
                        {{ $admin->is_admin ? 'Desactivar' : 'Activar' }}
                      </button>
                    </form>
                  @endif

                  @if(auth()->id() !== $admin->id)
                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" onsubmit="return confirm('¿Eliminar esta cuenta de administrador? Esta acción no se puede deshacer.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm text-red-600 hover:underline">Eliminar</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay cuentas administrativas registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white border rounded-2xl p-6">
    <h2 class="text-lg font-semibold mb-4">Crear nueva cuenta admin</h2>
    <form method="POST" action="{{ route('admin.admins.store') }}" class="grid gap-4 md:grid-cols-2">
      @csrf
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
      </div>
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Correo</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Contraseña temporal</label>
        <input type="password" name="password" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Rol</label>
        <select name="admin_role" class="mt-1 w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
          <option value="" disabled {{ old('admin_role') ? '' : 'selected' }}>Seleccione un rol</option>
          <option value="{{ \App\Models\User::ROLE_MASTER }}" @selected(old('admin_role') === \App\Models\User::ROLE_MASTER)>Admin Master</option>
          <option value="{{ \App\Models\User::ROLE_OPERATOR }}" @selected(old('admin_role') === \App\Models\User::ROLE_OPERATOR)>Admin Usuario</option>
        </select>
      </div>
      <div class="md:col-span-2 flex justify-end">
        <button type="submit" class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Crear administrador</button>
      </div>
    </form>
  </div>
</div>
@endsection
