export class FeatIcon extends HTMLDivElement {

    private readonly description: string;
    private readonly name: string;

    constructor() {
        super();

        if (this.dataset.description === undefined) {
            throw new Error('No description provided');
        }
        this.description = this.dataset.description;

        if (this.dataset.featname === undefined) {
            throw new Error('No name provided');
        }
        this.description = this.dataset.description;
        this.name = this.dataset.featname;
    }

    connectedCallback() {
        let popoverElement: HTMLDivElement = document.createElement('div');
        let headingElement = document.createElement("strong");
        let descriptionElement = document.createElement("p");


        headingElement.innerHTML = this.name;
        descriptionElement.innerHTML = this.description;
        popoverElement.appendChild(headingElement);
        popoverElement.appendChild(descriptionElement);

        popoverElement.classList.add('feat-description-popover');
        this.append(popoverElement);
        if (this.classList.contains('sm')) {
            this.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();

                let allElements = document.querySelectorAll('[is="feat-icon"]');
                allElements.forEach((element: Element) => {
                    if (element !== this) {
                        element.classList.remove('show-popover');
                    }
                });
                this.classList.toggle('show-popover');

            })
        }
    }
}
