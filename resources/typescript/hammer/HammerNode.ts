export default class HammerNode extends HTMLButtonElement {

    isRotated: boolean = false;

    isActivated: boolean = false;

    isBroken: boolean = false;

    static observedAttributes = ['data-active', 'data-rotated'];

    constructor() {
        super();
    }

    connectedCallback() {

        this.isActivated = this.dataset.active === 'true';
        this.isRotated = this.dataset.rotated === 'true';
        this.isBroken = this.dataset.broken === 'true';
        this.setStatuses();

        //Clicking a node acitvates its activands.
        if (!this.isBroken) {
            this.addEventListener('click', () => {
                this.dataset.rotated = this.isRotated ? 'false' : 'true';
                let event = new CustomEvent('hammer-node-rotated',
                    {
                        bubbles: true,
                        detail: {
                            id: this.id,
                            isRotated: this.isRotated,
                            isActive: this.isActivated,
                            nodeToActivate: this.isRotated ? this.dataset.activatesrotated : this.dataset.activatesdefault,
                            nodeToDeactivate: this.isRotated ? this.dataset.activatesdefault : this.dataset.activatesrotated,
                        }
                    }
                )
                this.dispatchEvent(event);
            });
        }
    }

    private setStatuses(): void {
        if (!this.isBroken) {
            this.isActivated = this.dataset.active === 'true';
            this.isRotated = this.dataset.rotated === 'true';

            if (this.isActivated) {
                this.classList.add('active');
            } else {
                this.classList.remove('active');
            }

            if (this.isRotated) {
                this.classList.add('rotated');
            } else {
                this.classList.remove('rotated');
            }
        } else {
            this.classList.add('broken');
        }

    }

    attributeChangedCallback() {
        this.setStatuses();
    }
}
