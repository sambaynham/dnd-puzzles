export class FlashAlert extends HTMLSpanElement {

    constructor() {
        super();
        let closeButton:HTMLButtonElement = document.createElement('button');
        closeButton.classList.add('close');
        closeButton.classList.add('unstyle');
        closeButton.innerHTML = '&times;';
        closeButton.addEventListener('click', () => {
            this.remove();
        })
        this.appendChild(closeButton);
    }
    public connectedCallback(): void {

    }
}
