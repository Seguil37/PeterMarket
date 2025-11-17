@extends('layouts.admin')
@section('admin-content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">Editar administrador</h1>
    <p class="text-sm text-gray-500">Actualiza la información o el estado del usuario.</p>
  </div>
  <a href="{{ route('admin.admins.index') }}" class="text-sm text-gray-600">Volver</a>
</div>

@if($errors->any())
  <div class="mb-4 rounded bg-red-100 p-3 text-red-700">
    <ul class="list-disc list-inside text-sm">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="bg-white border rounded p-6 space-y-6">
  @csrf
  @method('PUT')
  @include('admin.users._form', ['admin' => $admin])
  <div class="flex justify-end gap-3">
    <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
    <button class="px-4 py-2 bg-black text-white rounded">Guardar cambios</button>
  </div>
</form>
@endsection
