
import calculateDamage from "../helpers/DamageCalculator";

export default class HammerFiringButton extends HTMLButtonElement {
    connectedCallback(): void {
        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            if (this.getAttribute('disabled') === null) {
                this.dispatchEvent(new CustomEvent('hammer-fired-event', {
                    bubbles: true,
                    detail: {
                        'target': this.innerText,
                        'damage': calculateDamage(20, 10)
                    }
                }))
                this.setAttribute('disabled','true');
            }
        })
    }

}
