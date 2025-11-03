import {AccordionHeadingClickDetail} from "./Types/AccordionHeadingClickDetail";

export default class AccordionList extends HTMLDivElement {

    private entries: NodeListOf<HTMLDivElement>

    constructor() {
        super();
        this.entries = this.querySelectorAll('.accordion-entry');
    }
    connectedCallback() {
        this.addEventListener('accordion-heading-clicked', (event: CustomEventInit<AccordionHeadingClickDetail>) => {

            if (event.detail?.target !== undefined) {
                console.log(event.detail.target);
            }
        });
    }
}
