import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
  const fileInput = document.querySelector('[data-image-input]');
  const previewContainer = document.getElementById('previewContainer');
  const previewImage = document.getElementById('previewImagen');
  const previewCaption = document.getElementById('previewCaption');

  if (!fileInput || !previewContainer || !previewImage || !previewCaption) {
    return;
  }

  let objectUrl = null;
  const initialSrc = previewImage.dataset.initialSrc || '';
  const initialCaption = previewCaption.dataset.initialText || previewCaption.textContent || '';

  const showPreview = (src, caption) => {
    if (!src) return;
    previewImage.src = src;
    previewImage.alt = caption || 'Vista previa de la imagen seleccionada';
    previewCaption.textContent = caption;
    previewContainer.classList.remove('hidden');
  };

  const resetPreview = () => {
    if (initialSrc) {
      showPreview(initialSrc, initialCaption);
    } else {
      previewImage.removeAttribute('src');
      previewCaption.textContent = 'Vista previa de la imagen seleccionada';
      previewContainer.classList.add('hidden');
    }
  };

  fileInput.addEventListener('change', (event) => {
    const [file] = event.target.files || [];

    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }

    if (!file) {
      resetPreview();
      return;
    }

    objectUrl = URL.createObjectURL(file);
    showPreview(objectUrl, 'Vista previa de la imagen seleccionada');
  });

  window.addEventListener('beforeunload', () => {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
    }
  });
});
