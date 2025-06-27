
export default class DropCap extends HTMLParagraphElement {

    private initial:string;

    constructor() {
        super();
        let initial = this.innerHTML?.charAt(0);

        if (initial === undefined) {
            throw new Error('DropCap requires text content');
        }
        if (initial === ' ') {
            throw new Error('DropCap requires the first character to be a letter, not a space.');
        }

        this.initial = initial;
    }
     connectedCallback() {
        const content = document.createTextNode(this.initial);
        const span = document.createElement('span');


        span.classList.add('dc');
        span.classList.add('dc-'+this.initial.toLowerCase());
        span.appendChild(content);

        this.innerHTML = this.innerHTML.substring(1);
        this.insertBefore(span, this.firstChild);
    }
}
