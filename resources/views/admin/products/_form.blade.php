@php($p = $product ?? null)
@php($usesUploadedImage = $p?->hasUploadedImage())
@php($initialPreview = old('image_url', $p->image_url ?? null))
@php($hasPreview = filled($initialPreview))
@php($previewMessage = $p && $p->image_url
    ? ($usesUploadedImage ? 'Imagen actual (subida previamente).' : 'Imagen actual (desde enlace).')
    : 'Vista previa de la imagen seleccionada.')
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

    <div class="space-y-3">
      <div>
        <label class="block text-sm font-medium">Subir imagen</label>
        <input type="file" name="image_file" accept="image/png" data-image-input class="w-full border rounded px-3 py-2 bg-white">
        <p class="text-xs text-gray-500">Solo se permiten imágenes PNG (máx. 4MB).</p>
        @error('image_file') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>

      <div id="previewContainer"
           class="flex items-center gap-4 p-3 border rounded bg-gray-50 {{ $hasPreview ? '' : 'hidden' }}">
        <img id="previewImagen"
             data-initial-src="{{ $hasPreview ? $initialPreview : '' }}"
             src="{{ $hasPreview ? $initialPreview : '' }}"
             alt="Vista previa de la imagen"
             class="w-24 h-24 object-cover rounded border">
        <div class="text-sm text-gray-600">
          <p class="font-semibold text-gray-900" id="previewCaption" data-initial-text="{{ $previewMessage }}">{{ $previewMessage }}</p>
          @if($usesUploadedImage)
            <p>Actualmente este producto usa una imagen subida. Puedes seleccionar otra y se actualizará la vista previa.</p>
          @elseif($p && $p->image_url)
            <p>Imagen cargada desde enlace. Selecciona un archivo PNG para reemplazarla.</p>
          @else
            <p>La vista previa se mostrará automáticamente al elegir un archivo.</p>
          @endif
        </div>
      </div>

      <div class="flex flex-wrap gap-3">
        <button type="submit" id="btnCargarImagen" class="bg-black text-white px-4 py-2 rounded">Cargar imagen</button>
        <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      </div>
    </div>
  </div>
</form>
