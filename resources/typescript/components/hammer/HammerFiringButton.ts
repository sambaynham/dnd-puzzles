export default class HammerFiringButton extends HTMLButtonElement {
    connectedCallback(): void {
        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            if (this.getAttribute('disabled') === null) {
                this.dispatchEvent(new CustomEvent('hammer-fired-event', {
                    bubbles: true,
                    detail: {
                        'target': this.innerText,
                        'damage': this.calculateDamage()
                    }
                }))
                this.setAttribute('disabled','true');
            }
        })
    }

    private calculateDamage(): number[] {
        let dieSides = 20;
        let diceCount: number = 10;
        let rolls: number[] = [];
        for(let i: number = 0; i < diceCount; i++) {
            rolls.push(this.rollDie(dieSides));
        }
        return rolls;
    }

    private rollDie(dieSides:number): number {
        return Math.floor(Math.random() * dieSides) + 1;
    }
}
