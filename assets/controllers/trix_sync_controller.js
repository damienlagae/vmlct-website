import { Controller } from '@hotwired/stimulus';

/**
 * Bridges Trix's `trix-change` event to a plain `change` event on the
 * connected hidden input — Trix already keeps the input's `value` in
 * sync, but Live Component's `data-model` only listens for native
 * `change` / `input` events.
 *
 * Usage: <trix-editor input="my-hidden-id" data-controller="trix-sync">
 */
export default class extends Controller {
    connect() {
        this._onTrixChange = this._onTrixChange.bind(this);
        this._onFileAccept = (event) => event.preventDefault();
        this.element.addEventListener('trix-change', this._onTrixChange);
        // Block file attachments — uploads must go through Flysystem/Liip,
        // not Trix's default attach-and-base64 flow.
        this.element.addEventListener('trix-file-accept', this._onFileAccept);
    }

    disconnect() {
        this.element.removeEventListener('trix-change', this._onTrixChange);
        this.element.removeEventListener('trix-file-accept', this._onFileAccept);
    }

    _onTrixChange() {
        const inputId = this.element.getAttribute('input');
        if (!inputId) {
            return;
        }
        const hidden = document.getElementById(inputId);
        if (!hidden) {
            return;
        }
        hidden.dispatchEvent(new Event('change', { bubbles: true }));
    }
}
