import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'results'];

    #timer = null;

    search() {
        clearTimeout(this.#timer);
        this.#timer = setTimeout(() => this.#fetchResults(), 300);
    }

    async #fetchResults() {
        const query = this.inputTarget.value.trim();

        if (query.length < 2) {
            this.resultsTarget.innerHTML = '';
            return;
        }

        this.resultsTarget.innerHTML = '<p class="loading">Recherche en cours…</p>';

        const response = await fetch(`/search/results?q=${encodeURIComponent(query)}`);
        this.resultsTarget.innerHTML = await response.text();
    }

    async add(event) {
        const btn = event.currentTarget;
        const data = {
            title: btn.dataset.bookTitle,
            author: btn.dataset.bookAuthor || null,
            firstPublishYear: btn.dataset.bookYear || null,
            coverUrl: btn.dataset.bookCoverUrl || null,
            key: btn.dataset.bookKey,
        };

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>';

        const response = await fetch('/search/add', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });

        if (response.ok || response.status === 409) {
            btn.innerHTML = '✓';
            btn.classList.remove('bg-blue-500', 'hover:bg-blue-700', 'hover:scale-110', 'active:scale-95');
            btn.classList.add('bg-green-500');
        } else {
            btn.innerHTML = original;
            btn.disabled = false;
        }
    }
}
