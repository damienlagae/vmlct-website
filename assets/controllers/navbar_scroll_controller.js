import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        threshold: { type: Number, default: 50 },
    };

    connect() {
        this.handleScroll = this.handleScroll.bind(this);
        window.addEventListener('scroll', this.handleScroll, { passive: true });
        this.handleScroll();
    }

    disconnect() {
        window.removeEventListener('scroll', this.handleScroll);
    }

    handleScroll() {
        this.element.classList.toggle('scrolled', window.scrollY > this.thresholdValue);
    }
}
