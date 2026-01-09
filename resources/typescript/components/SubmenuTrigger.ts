export class SubmenuTrigger extends HTMLAnchorElement {

    private targetSubmenu: HTMLElement;

    constructor() {
        super();
        let targetId: string|undefined = this.dataset.targetsubmenu;
        if (targetId === undefined) {
            throw new Error('Data Target is undefined');
        }

        let targetSubmenu: HTMLElement|null = document.getElementById(targetId);

        if (targetSubmenu === null) {
            throw new Error('Submenu target is not present in DOM');
        }

        this.targetSubmenu = targetSubmenu;
    }
    connectedCallback() {
        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            this.targetSubmenu.classList.toggle('show');
        });

        this.addEventListener('tap', (e: Event) => {
            e.preventDefault();
            this.targetSubmenu.classList.toggle('show');
        });
    }
}
