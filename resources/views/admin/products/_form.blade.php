@php($p = $product ?? null)
<form method="POST" action="{{ $route }}" class="grid gap-4 max-w-xl">
  @csrf
  @if($method !== 'POST') @method($method) @endif

  <div>
    <label class="block text-sm mb-1">Nombre</label>
    <input name="name" value="{{ old('name', $p->name ?? '') }}" class="w-full border rounded px-3 py-2">
    @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm mb-1">Descripción</label>
    <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $p->description ?? '') }}</textarea>
    @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Precio</label>
      <input type="number" step="0.01" name="price" value="{{ old('price', $p->price ?? '') }}" class="w-full border rounded px-3 py-2">
      @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
      <label class="block text-sm mb-1">Stock</label>
      <input type="number" name="stock" value="{{ old('stock', $p->stock ?? '') }}" class="w-full border rounded px-3 py-2">
      @error('stock') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
  </div>

  <div>
    <label class="block text-sm mb-1">Imagen (URL)</label>
    <input name="image_url" value="{{ old('image_url', $p->image_url ?? '') }}" class="w-full border rounded px-3 py-2">
    @error('image_url') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  <div class="flex gap-2">
    <button class="bg-black text-white px-4 py-2 rounded">Guardar</button>
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
  </div>
</form>
