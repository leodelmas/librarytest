import { Controller } from '@hotwired/stimulus';

const SORT_ORDER = { reading: 0, to_read: 1, read: 2 };

export default class extends Controller {
    static values = { url: String, statusUrl: String };
    static targets = ['button', 'statusBtn', 'statusWrap', 'statusLabel'];

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

    async changeStatus() {
        const response = await fetch(this.statusUrlValue, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
        });

        if (response.ok) {
            const data = await response.json();
            const [bg, text] = data.colors;

            const btn = this.statusBtnTarget;
            btn.style.backgroundColor = bg;
            btn.style.color = text;
            btn.querySelectorAll('[data-status]').forEach((icon) => {
                icon.classList.toggle('hidden', icon.dataset.status !== data.status);
            });

            this.statusWrapTarget.dataset.status = data.status;

            const label = this.statusLabelTarget;
            label.textContent = data.label;
            label.style.backgroundColor = bg;
            label.style.color = text;

            const grid = this.element.parentElement;
            const cards = [...grid.children];
            const order = (el) => {
                const wrap = el.querySelector('[data-book-collection-target="statusWrap"]');
                return wrap ? SORT_ORDER[wrap.dataset.status] ?? 3 : 3;
            };
            cards.sort((a, b) => order(a) - order(b));
            cards.forEach((card) => grid.appendChild(card));
        }
    }
}
