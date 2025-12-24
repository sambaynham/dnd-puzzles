import {AccordionHeadingClickDetail} from "./Types/AccordionHeadingClickDetail";

export default class AccordionList extends HTMLDivElement {

    private entries: NodeListOf<HTMLDivElement>

    constructor() {
        super();
        this.entries = this.querySelectorAll('.accordion-entry');
        if (this.entries.length === 1 && this.entries[0]!== undefined) {
            this.entries[0].classList.add('open');
        }
    }

    connectedCallback() {

        this.addEventListener('accordion-heading-clicked', (event: CustomEventInit<AccordionHeadingClickDetail>) => {
            if (event.detail?.target !== undefined) {
                this.entries.forEach((listItem: HTMLElement)=> {
                    if (event.detail !== undefined && listItem !== event.detail.target) {
                        listItem.classList.remove('open');
                    }
                });
                event.detail.target.classList.toggle('open');
                this.dispatchEvent(new CustomEvent('child-element-resize', {bubbles: true, composed: true}));
            }
        });
    }
}
