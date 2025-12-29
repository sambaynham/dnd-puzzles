export class FlashAlert extends HTMLSpanElement {

    constructor() {
        super();
        this.classList.add('show');
        let closeButton:HTMLButtonElement = document.createElement('button');
        closeButton.classList.add('close');
        closeButton.classList.add('unstyle');
        closeButton.innerHTML = '&times;';
        closeButton.addEventListener('click', () => {
            this.classList.remove('show')
        })
        this.appendChild(closeButton);

    }
    public connectedCallback(): void {
        setInterval(()=> this.classList.remove('show'), 10000);
    }
}
