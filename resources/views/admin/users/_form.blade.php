<div class="space-y-4">
  <div>
    <label class="block text-sm font-medium mb-1">Nombre completo</label>
    <input type="text" name="name" value="{{ old('name', $admin->name ?? '') }}" class="w-full border rounded px-3 py-2" required>
    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
  <div>
    <label class="block text-sm font-medium mb-1">Correo</label>
    <input type="email" name="email" value="{{ old('email', $admin->email ?? '') }}" class="w-full border rounded px-3 py-2" required>
    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Contraseña @if(!isset($admin))<span class="text-gray-500 text-xs">(mínimo 8 caracteres)</span>@endif</label>
      <input type="password" name="password" class="w-full border rounded px-3 py-2" @if(!isset($admin)) required @endif>
      @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
      <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" @if(!isset($admin)) required @endif>
    </div>
  </div>
  <div class="flex flex-col gap-2">
    <label class="inline-flex items-center gap-2">
      <input type="hidden" name="is_active" value="0">
      <input type="checkbox" name="is_active" value="1" class="h-4 w-4" @checked(old('is_active', $admin->is_active ?? true))>
      <span>Cuenta activa</span>
    </label>
    <label class="inline-flex items-center gap-2">
      <input type="hidden" name="is_master_admin" value="0">
      <input type="checkbox" name="is_master_admin" value="1" class="h-4 w-4" @checked(old('is_master_admin', $admin->is_master_admin ?? false)) @disabled(isset($admin) && auth()->id() === $admin->id)>
      <span>Admin Master</span>
    </label>
    @if(isset($admin) && auth()->id() === $admin->id)
      <p class="text-sm text-gray-500">Eres el Admin Master actual, este permiso no puede desactivarse desde tu propia cuenta.</p>
    @endif
    @error('is_master_admin')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
  </div>
</div>
