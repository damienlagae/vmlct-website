import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['link', 'section'];

    connect() {
        this.observer = new IntersectionObserver(
            (entries) => {
                if (window.scrollY < 200) return;
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        this.setActive(`#${entry.target.id}`);
                    }
                });
            },
            { rootMargin: '-50% 0px -50% 0px' },
        );

        this.sectionTargets.forEach((section) => this.observer.observe(section));

        this.handleScroll = this.handleScroll.bind(this);
        window.addEventListener('scroll', this.handleScroll, { passive: true });
        this.setActive('#home');
    }

    disconnect() {
        this.observer?.disconnect();
        window.removeEventListener('scroll', this.handleScroll);
    }

    handleScroll() {
        if (window.scrollY < 200) {
            this.setActive('#home');
        }
    }

    setActive(href) {
        this.linkTargets.forEach((link) => {
            link.classList.toggle('active', link.getAttribute('href') === href);
        });
    }
}
