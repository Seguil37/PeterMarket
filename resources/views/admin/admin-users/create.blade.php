@extends('layouts.app')
@section('title','Nuevo administrador')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <div class="bg-white border rounded-2xl p-6">
    <h1 class="text-xl font-semibold mb-4">Registrar administrador</h1>

    @if ($errors->any())
      <div class="mb-4 rounded bg-red-50 border border-red-200 text-red-700 px-4 py-3">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-6">
      @csrf
      @include('admin.admin-users.partials.form')

      <div class="flex justify-end gap-3">
        <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg border text-gray-700">Cancelar</a>
        <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Guardar</button>
      </div>
    </form>
  </div>
</div>
@endsection
