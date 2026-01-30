import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['dialog', 'body'];
    static values = { url: String };

    connect() {
        this.element.addEventListener('note-modal:close', () => this.close());
    }

    async open(event) {
        event.preventDefault();
        const url = event.currentTarget.getAttribute('href');

        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!response.ok) return;

        this.bodyTarget.innerHTML = await response.text();
        this.dialogTarget.showModal();
    }

    close() {
        this.dialogTarget.close();
        this.bodyTarget.innerHTML = '';
    }

    backdropClick(event) {
        if (event.target === this.dialogTarget) {
            this.close();
        }
    }
}
