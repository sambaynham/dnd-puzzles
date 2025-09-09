export default class HammerResetButton extends HTMLButtonElement {
    public connectedCallback(): void {
        this.addEventListener('click',  () => {
            console.log('reset requested');
            this.dispatchEvent(new CustomEvent('reset-button-clicked', {
                bubbles: true
            }))
        });
    }
}
