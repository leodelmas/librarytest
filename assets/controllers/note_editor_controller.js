import { Controller } from '@hotwired/stimulus';
import Quill from 'quill';

export default class extends Controller {
    static targets = ['editor', 'content', 'status'];
    static values = { url: String };

    connect() {
        this.quill = new Quill(this.editorTarget, {
            theme: 'bubble',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link'],
                    ['clean'],
                ],
            },
        });

        if (this.contentTarget.value) {
            this.quill.root.innerHTML = this.contentTarget.value;
        }
    }

    async save() {
        const html = this.quill.root.innerHTML;
        this.statusTarget.textContent = 'Sauvegarde...';
        this.statusTarget.className = 'text-sm text-gray-500';

        try {
            const response = await fetch(this.urlValue, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ content: html }),
            });

            if (response.ok) {
                this.statusTarget.textContent = 'Sauvegardé !';
                this.statusTarget.className = 'text-sm text-green-600';
            } else {
                throw new Error();
            }
        } catch {
            this.statusTarget.textContent = 'Erreur lors de la sauvegarde';
            this.statusTarget.className = 'text-sm text-red-600';
        }
    }

    close() {
        this.element.closest('[data-controller="note-modal"]')
            ?.dispatchEvent(new CustomEvent('note-modal:close'));
    }
}
