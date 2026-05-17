import { Controller } from '@hotwired/stimulus';

/**
 * Mirrors the cover image into a blurred background layer so a wide hero
 * keeps the original image intact (object-fit: contain) while still
 * filling the full viewport width visually.
 *
 * Usage:
 *   <figure class="news-article__hero" data-controller="hero-blur"
 *           data-hero-blur-src-value="{{ heroUrl }}">
 */
export default class extends Controller {
    static values = { src: String };

    connect() {
        if (!this.srcValue) {
            return;
        }
        const layer = document.createElement('div');
        layer.className = 'news-article__hero-bg';
        layer.style.backgroundImage = `url("${this.srcValue}")`;
        this.element.prepend(layer);
        this._layer = layer;
    }

    disconnect() {
        this._layer?.remove();
    }
}
