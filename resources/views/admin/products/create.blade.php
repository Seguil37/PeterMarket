@extends('layouts.admin')
@section('admin-content')
  <h2 class="text-xl font-semibold mb-4">Nuevo producto</h2>
  @include('admin.products._form', ['route' => route('admin.products.store'), 'method' => 'POST'])
@endsection
