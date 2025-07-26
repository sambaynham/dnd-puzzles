import {NodeDetail} from "./Types/NodeDetail";

export default class HammerNode extends HTMLButtonElement {

    private isBroken: boolean = false;

    static observedAttributes = ['data-active'];

    constructor() {
        super();
    }

    connectedCallback() {

        this.isBroken = this.dataset.broken === 'true';
        if (this.dataset.active === 'true') {
            this.classList.add('active');
        }
        //Clicking a node rotates it.
        if (!this.isBroken) {
            this.addEventListener('click', (e: MouseEvent) => {
                if (this.getAttribute('disabled') !== 'true' ) {
                    e.preventDefault();
                    this.rotate();
                }
            });
        } else {
            this.classList.add('broken')
        }
    }


    private rotate(): void {

        this.dataset.rotated = this.dataset.rotated === 'true' ? 'false' : 'true';

        let event = new CustomEvent('hammer-node-rotated', {
            bubbles: true,
            cancelable: false,
            detail: this.generateNodeDetails()
        });
        this.updateClasses();
        this.dispatchEvent(event);

    }

    public generateNodeDetails(): NodeDetail {
        return {
            id: this.id,
            isActive: this.dataset.active === 'true',
            isBroken: this.dataset.broken === 'true',
            isRotated: this.dataset.rotated === 'true',
            activatesDefault: this.dataset.activatesdefault ?? '',
            activatesRotated: this.dataset.activatesrotated ?? ''
        };
    }

    private updateClasses(): void {
        if (this.dataset.active === 'true') {
            this.classList.add('active');
        } else {
            this.classList.remove('active');
        }

        if (this.dataset.rotated === 'true') {
            this.classList.add('rotated');
        } else {
            this.classList.remove('rotated');
        }
    }

    attributeChangedCallback(name: string, oldValue: string, newValue: string) {
        if (name === 'data-active') {
            if (oldValue !== 'true' && newValue === 'true') {
                this.dispatchEvent(new CustomEvent('hammer-node-activated', {
                    bubbles: true,
                    cancelable: false,
                    detail: this.generateNodeDetails()
                }));
            } else if (oldValue === 'true' && newValue !== 'true') {
                this.dispatchEvent(new CustomEvent('hammer-node-deactivated', {
                    bubbles: true,
                    cancelable: false,
                    detail: this.generateNodeDetails()
                }));
            }
        }

        this.updateClasses();
    }
}
