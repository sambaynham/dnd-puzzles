import {AccordionHeadingClickDetail} from "./Types/AccordionHeadingClickDetail";

export class AccordionEntry extends HTMLElement {

    static observedAttributes = ['data-open'];

    connectedCallback() {
        let headerElement: HTMLHeadingElement|null = this.querySelector('.accordion-heading');

        if (null === headerElement) {
            throw new Error('Malformed list; no heading detected. Aborting');
        }

        headerElement.addEventListener('click', (e:MouseEvent) => {
            e.preventDefault();
            this.dispatchEvent(new CustomEvent(
                'accordion-heading-clicked',
                {
                    bubbles: true,
                    cancelable: false,
                    detail: this.generateDetail()
                }
            ));
        });
    }

    private generateDetail(): AccordionHeadingClickDetail {
        return {
            target: this
        };
    }

    attributeChangedCallback(name: string, newValue: string) {
        if (name === 'data-active') {
            if (newValue === 'true') {
                this.classList.add('open')
            } else {
                this.classList.remove('open')
            }

        }
    }
}
