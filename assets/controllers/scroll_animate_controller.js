import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        threshold: { type: Number, default: 0.1 },
    };

    connect() {
        this.element.classList.add('scroll-animate');

        this.observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        this.observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: this.thresholdValue },
        );

        this.observer.observe(this.element);
    }

    disconnect() {
        this.observer?.disconnect();
    }
}
