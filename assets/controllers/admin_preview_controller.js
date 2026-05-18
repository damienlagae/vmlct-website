import { Controller } from '@hotwired/stimulus';

/**
 * Drives the side-by-side admin preview pane:
 *   - toggles visibility (persisted in localStorage)
 *   - exposes a manual "refresh" action so editors can force-reload the
 *     iframe after a save without scrolling back to the form button.
 *
 * Layout root: `<div class="admin-with-preview" data-controller="admin-preview" data-admin-preview-key-value="article|page">`
 */
export default class extends Controller {
    static targets = ['frame', 'toggle', 'wrapper'];
    static values = {
        key: String, // localStorage scope (article|page|...)
        url: String, // preview iframe src — used for manual refresh
    };

    connect() {
        this._storageKey = `admin-preview:${this.keyValue || 'default'}`;
        const stored = localStorage.getItem(this._storageKey);
        // Default = visible on first load
        const visible = stored === null ? true : stored === '1';
        this._apply(visible);
    }

    toggle() {
        const next = !this._wrapper().classList.contains('admin-with-preview--open');
        this._apply(next);
        localStorage.setItem(this._storageKey, next ? '1' : '0');
    }

    refresh() {
        if (!this.hasFrameTarget) {
            return;
        }
        const url = new URL(this.urlValue, window.location.origin);
        url.searchParams.set('_t', Date.now().toString());
        this.frameTarget.src = url.toString();
    }

    _apply(visible) {
        this._wrapper().classList.toggle('admin-with-preview--open', visible);
        if (this.hasToggleTarget) {
            this.toggleTarget.setAttribute('aria-pressed', visible ? 'true' : 'false');
        }
    }

    _wrapper() {
        return this.hasWrapperTarget ? this.wrapperTarget : this.element;
    }
}
