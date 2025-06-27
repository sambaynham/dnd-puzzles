export default class ProgressBar extends HTMLElement {


    private progress: number = 0;
    constructor() {
        super();
        if (isNaN(Number(this.dataset.progress))){
            throw new Error('Progress must be a number');
        }

        this.progress = Number(this.dataset.progress);
    }

    connectedCallback() {
        const span = document.createElement('span');
        span.style.width = `${this.progress}%`;
        this.appendChild(span);
    }
}
