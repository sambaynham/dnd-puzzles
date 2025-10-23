export class Header extends HTMLElement {
    constructor() {
        super();
    }

    public connectedCallback(): void {
        document.addEventListener('scroll', () => {
            if (document.body.scrollTop > this.offsetTop || document.documentElement.scrollTop > (this.offsetTop / 2)) {
                document.body.classList.add('scrolled');
            } else {
                document.body.classList.remove('scrolled');
            }
        });
    }
}
