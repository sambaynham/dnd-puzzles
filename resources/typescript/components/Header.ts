export class Header extends HTMLElement {

    private observer : IntersectionObserver;

    private bodyElement: HTMLBodyElement;

    private logoWrapper: HTMLElement;

    constructor() {
        super();
        const options = {
            rootMargin: "0px",
            scrollMargin: "0px",
            threshold: 0.0,
        }

        let body: HTMLBodyElement|null = document.querySelector('body');
        let logoWrapper: HTMLElement|null = document.getElementById('logo-wrapper');

        if (body === null || logoWrapper === null) {
            throw new Error('Header requires a body and logo wrapper');
        }
        this.bodyElement = body;
        this.logoWrapper = logoWrapper;

        this.observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.bodyElement.classList.remove('scrolled');
                } else {
                    this.bodyElement.classList.add('scrolled');
                }
            })
        }, options);

        this.observer.observe(this.logoWrapper);
    }

    public connectedCallback(): void {

    }
}
