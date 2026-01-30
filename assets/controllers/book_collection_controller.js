import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = { url: String };
    static targets = ['button'];

    async delete() {
        if (!confirm('Supprimer ce livre de votre collection ?')) return;

        const btn = this.buttonTarget;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>';

        const response = await fetch(this.urlValue, { method: 'DELETE' });
        if (response.ok) {
            this.element.style.transition = 'opacity 0.3s, transform 0.3s';
            this.element.style.opacity = '0';
            this.element.style.transform = 'scale(0.95)';
            setTimeout(() => this.element.remove(), 300);
        } else {
            btn.innerHTML = original;
            btn.disabled = false;
        }
    }
}
