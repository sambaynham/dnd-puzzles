export default class NavToggle extends HTMLButtonElement {

    static observedAttributes = ['data-open'];

    private targetNav: HTMLElement;

    private otherNavToggles: NodeListOf<HTMLButtonElement>;

    private body: HTMLBodyElement;

    constructor() {
        super();

        let targetNav: HTMLElement|null = document.getElementById(this.dataset.target as string);
        if (targetNav === null) {
            throw new Error('Target nav not found');
        }
        let body = document.querySelector('body');
        if (body === null) {
            throw new Error("No body element found");
        }
        this.body = body;
        this.targetNav = targetNav;
        this.otherNavToggles = document.querySelectorAll('button.nav-toggle');

    }
    public connectedCallback(): void {
        this.setupListeners();
    }

    private setupListeners(): void {
        this.addEventListener('click', () => {
            this.dataset.open = this.dataset.open === 'true' ? 'false' : 'true';
            this.otherNavToggles.forEach((toggle: HTMLButtonElement) => {
                if (toggle !== this) {
                    toggle.dataset.open = 'false';
                }
            });
            let navOpen = false;
            this.otherNavToggles.forEach((toggle: HTMLButtonElement) => {
                if (toggle.dataset.open === 'true') {
                    navOpen = true;
                }
            });
            if (navOpen) {
                this.body.classList.add('nav-open');
            } else {
                this.body.classList.remove('nav-open');
            }

        });
    }

    public attributeChangedCallback(name: string, oldValue: string, newValue: string) {
        if (name === 'data-open') {
            if (oldValue !== 'true' && newValue === 'true') {
                this.classList.add('open');
                this.targetNav.classList.add('open');
            } else if (oldValue === 'true' && newValue !== 'true') {
                this.targetNav.classList.remove('open');
                this.classList.remove('open');
            }
        }
    }
}
