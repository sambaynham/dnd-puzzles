export default class HammerNode extends HTMLButtonElement {
    constructor() {
        super();
    }

    connectedCallback() {
        if (!this.classList.contains('broken')) {
            this.addEventListener('click', () => {
                this.classList.toggle('rotated');
                let rotated = this.classList.contains('rotated');
                let event = new CustomEvent('hammer-node-rotated', {
                    bubbles: true,
                    detail: {
                        id: this.id,
                        rotated: this.classList.contains('rotated'),
                        nodesToActivate: rotated ? this.dataset.activatesrotated : this.dataset.activatesdefault,
                    }
                })
                this.dispatchEvent(event);
            });
        }

    }
}
