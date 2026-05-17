import { Controller } from '@hotwired/stimulus';

/**
 * Shows a live preview of the image the user is about to upload,
 * right next to the file input. Attach with data-controller="image-preview"
 * on the <input type="file"> element (or on a wrapper that contains one).
 */
export default class extends Controller {
    connect() {
        this.fileInput =
            this.element.tagName === 'INPUT' && this.element.type === 'file'
                ? this.element
                : this.element.querySelector('input[type="file"]');

        if (!this.fileInput) {
            return;
        }

        this.fileInput.addEventListener('change', this.handleChange);
    }

    disconnect() {
        if (this.fileInput) {
            this.fileInput.removeEventListener('change', this.handleChange);
        }
    }

    handleChange = () => {
        const file = this.fileInput.files && this.fileInput.files[0];
        if (!file || !file.type.startsWith('image/')) {
            this.removePreview();
            return;
        }

        const reader = new FileReader();
        reader.onload = (event) => this.renderPreview(event.target.result);
        reader.readAsDataURL(file);
    };

    renderPreview(dataUrl) {
        const container = this.fileInput.parentElement;
        let img = container.querySelector('.image-preview__live');
        if (!img) {
            img = document.createElement('img');
            img.className = 'image-preview__live mt-2 rounded border bg-white';
            img.style.maxHeight = '160px';
            img.style.maxWidth = '100%';
            img.style.display = 'block';
            img.style.padding = '4px';
            container.appendChild(img);
        }
        img.src = dataUrl;
        img.alt = this.fileInput.files[0].name;
    }

    removePreview() {
        const img = this.fileInput.parentElement.querySelector('.image-preview__live');
        if (img) {
            img.remove();
        }
    }
}
