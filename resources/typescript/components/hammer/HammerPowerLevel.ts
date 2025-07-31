
export default class HammerPowerLevel  extends HTMLMeterElement implements HTMLMeterElement {
    static observedAttributes: string[] = ['value'];

    static successfulPowerLevel: number = 5;

    attributeChangedCallback(_name: string, _oldValue: string, newValue: string) {
        let newValueInteger = parseInt(newValue);

        if (newValueInteger == HammerPowerLevel.successfulPowerLevel) {
            this.dispatchEvent(new CustomEvent('puzzle-success-event', {
                bubbles: true,
            }));
        } else if (newValueInteger > HammerPowerLevel.successfulPowerLevel) {
            this.dispatchEvent(new CustomEvent('puzzle-overvolt-event', {
                bubbles: true,
            }));
        }
        this.setClasses();
    }

    private setClasses(): void {
        let currentValue: string|null = this.getAttribute('value');
        if (currentValue !== null) {
            let numericValue = parseFloat(currentValue);
            if (numericValue !== undefined) {
                if (numericValue > HammerPowerLevel.successfulPowerLevel) {
                    this.classList.add('overvolt');
                } else {
                    this.classList.remove('overvolt');
                }
            }
        }
    }
}
