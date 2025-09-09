export default class HammerTranslateButton extends HTMLButtonElement {
    public connectedCallback(): void {
        this.addEventListener('click', () => {
            this.dispatchEvent(new CustomEvent('translation-request', {
                bubbles: true
            }));
        });
    }
}
