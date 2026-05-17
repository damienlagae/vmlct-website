import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { message: String };

    connect() {
        this.element.addEventListener('submit', this.handleSubmit);
    }

    disconnect() {
        this.element.removeEventListener('submit', this.handleSubmit);
    }

    handleSubmit = (event) => {
        if (!window.confirm(this.messageValue)) {
            event.preventDefault();
        }
    };
}
