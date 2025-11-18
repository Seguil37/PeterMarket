@php($p = $product ?? null)
@php($usesUploadedImage = $p?->hasUploadedImage())
<form method="POST" action="{{ $route }}" class="grid gap-4 max-w-xl" enctype="multipart/form-data">
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

  <div class="space-y-2">
    <div>
      <label class="block text-sm font-medium">Imagen (enlace)</label>
      <input name="image_url" type="url"
             value="{{ old('image_url', $usesUploadedImage ? '' : ($p->image_url ?? '')) }}"
             placeholder="https://tusitio.com/imagen.jpg"
             class="w-full border rounded px-3 py-2">
      <p class="text-xs text-gray-500">Pega un enlace válido o deja el campo vacío si usarás la opción de subir archivo.</p>
      @error('image_url') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Subir imagen</label>
      <input type="file" name="image_file" accept="image/*" class="w-full border rounded px-3 py-2 bg-white">
      <p class="text-xs text-gray-500">Formatos permitidos: JPG, PNG, WEBP (máx. 4MB).</p>
      @error('image_file') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    @if($p && $p->image_url)
      <div class="flex items-center gap-4 p-3 border rounded bg-gray-50">
        <img src="{{ $p->image_url }}" alt="{{ $p->name }}" class="w-24 h-24 object-cover rounded">
        <div class="text-sm text-gray-600">
          <p class="font-semibold text-gray-900">Imagen actual</p>
          @if($usesUploadedImage)
            <p>Actualmente este producto usa una imagen subida. Deja los campos como están si deseas conservarla.</p>
          @else
            <p>Imagen cargada desde enlace.</p>
          @endif
        </div>
      </div>
    @endif
  </div>

  <div class="flex gap-2">
    <button class="bg-black text-white px-4 py-2 rounded">Guardar</button>
    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
  </div>
</form>
