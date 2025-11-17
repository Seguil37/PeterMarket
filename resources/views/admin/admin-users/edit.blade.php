@extends('layouts.app')
@section('title','Editar administrador')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <div class="bg-white border rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Editar administrador</h1>
      <a href="{{ route('admin.admins.index') }}" class="text-sm text-blue-600">&larr; Volver al listado</a>
    </div>

    @if ($errors->any())
      <div class="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-4 py-3">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="space-y-6">
      @csrf
      @method('PUT')
      @include('admin.admin-users.partials.form', ['admin' => $admin])

      <div class="flex justify-end gap-3">
        <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg border text-gray-700">Cancelar</a>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>
@endsection
