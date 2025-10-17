export class Header extends HTMLElement {
    constructor() {
        super();
    }

    public connectedCallback(): void {
        const headerBottom: number = this.offsetTop + this.offsetHeight;

        document.addEventListener('scroll', () => {
            if (document.body.scrollTop > headerBottom || document.documentElement.scrollTop > (headerBottom / 2)) {
                document.body.classList.add('scrolled');
            } else {
                document.body.classList.remove('scrolled');
            }
        });
    }
}
