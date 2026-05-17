import { Controller } from '@hotwired/stimulus';

/**
 * Drives the inline-upload flow for an ImageBlock in the ContentEditor
 * Live Component:
 *  - sends the picked file to the upload endpoint as multipart/form-data
 *  - writes the returned storage path into the bound hidden input
 *  - fires a `change` event so Live Component's `data-model` picks it up
 *
 * Targets:
 *  - input: hidden input that holds the block's `path` field
 *  - file:  visible <input type="file">
 *  - status: element that displays progress / error
 *  - preview: <img> that shows the picked file via object URL
 */
export default class extends Controller {
    static targets = ['input', 'file', 'status', 'preview'];
    static values = { url: String };

    upload(event) {
        const file = event.target.files?.[0];
        if (!file) {
            return;
        }

        this._setStatus('uploading');

        if (this.hasPreviewTarget) {
            this.previewTarget.src = URL.createObjectURL(file);
            this.previewTarget.hidden = false;
        }

        const formData = new FormData();
        formData.append('file', file);

        fetch(this.urlValue, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
        })
            .then(async (response) => {
                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.error || `HTTP ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                this.inputTarget.value = data.path;
                this.inputTarget.dispatchEvent(new Event('change', { bubbles: true }));
                this._setStatus('done');
            })
            .catch((err) => {
                this._setStatus('error', err.message);
            });
    }

    _setStatus(state, message = '') {
        if (!this.hasStatusTarget) {
            return;
        }
        this.statusTarget.dataset.state = state;
        this.statusTarget.textContent = message || this.statusTarget.dataset[state] || '';
    }
}
