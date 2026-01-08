export class LazyImage extends HTMLImageElement {

    connectedCallback() {
        let src: string|null = this.getAttribute('data-src');

        if (src === null) {
            throw new Error('No src attribute found on LazyImage');
        }

        this.setAttribute('src', src);

        this.setAttribute('aria-hidden', 'true');

        if (!this.complete) {
            this.addEventListener('load', () => {
                this.classList.add('loaded');
                this.setAttribute('aria-hidden', 'false');
            });
        }
    }
}
