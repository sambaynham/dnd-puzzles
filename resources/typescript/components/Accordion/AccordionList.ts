export default class AccordionList extends HTMLDivElement {

    private entries: NodeListOf<HTMLDivElement>

    constructor() {
        super();
        this.entries = this.querySelectorAll('.accordion-entry');
    }
    connectedCallback() {
        console.log(this.entries);
    }
}
