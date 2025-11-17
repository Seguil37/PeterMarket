@php
    $editing = isset($admin);
@endphp

<div class="grid gap-4">
  <div>
    <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
    <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Correo</label>
    <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
  </div>

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Contraseña {{ $editing ? '(opcional)' : '' }}</label>
      <input type="password" name="password" class="mt-1 w-full border rounded-lg px-3 py-2" {{ $editing ? '' : 'required' }}>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Confirmar contraseña {{ $editing ? '(opcional)' : '' }}</label>
      <input type="password" name="password_confirmation" class="mt-1 w-full border rounded-lg px-3 py-2" {{ $editing ? '' : 'required' }}>
    </div>
  </div>

  <div class="space-y-2">
    <input type="hidden" name="is_active" value="0">
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" class="h-4 w-4" {{ old('is_active', $admin->is_active ?? true) ? 'checked' : '' }}>
      <span class="text-sm text-gray-700">Cuenta activa</span>
    </label>
    <input type="hidden" name="is_master_admin" value="0">
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="is_master_admin" value="1" class="h-4 w-4" {{ old('is_master_admin', $admin->is_master_admin ?? false) ? 'checked' : '' }} @if(($masterCount ?? 0) >= 1 && !old('is_master_admin', $admin->is_master_admin ?? false)) disabled @endif>
      <span class="text-sm text-gray-700">Asignar como Admin Master</span>
    </label>
    @if(($masterCount ?? 0) >= 1 && !old('is_master_admin', $admin->is_master_admin ?? false))
      <p class="text-xs text-gray-500">Ya existe un Admin Master registrado. Debes editarlo antes de transferir el rol.</p>
    @endif
  </div>
</div>
