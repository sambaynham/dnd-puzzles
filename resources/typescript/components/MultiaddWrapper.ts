export class MultiaddWrapper extends HTMLElement {

    private prototype: HTMLElement;

    private addMoreButton: HTMLButtonElement;

    constructor() {
        super();
        let prototypeString: string|undefined = this.dataset.prototype;
        let addMoreButton: HTMLButtonElement|null = this.querySelector('button.multiform-add-row-button';)

        if (null === addMoreButton) {
            throw new Error('An add more button could not be found');
        }

        this.addMoreButton = addMoreButton;

        if (typeof prototypeString !== 'string') {
            throw new Error('The prototype field must be a string');
        }

        this.prototype = this.generatePrototypeNode(prototypeString);

        this.addMoreButton.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            let currentRows = this.querySelectorAll('div.multiadd-row');
            this.appendChild(this.createNewRow(currentRows.length))
        });

        let children: NodeListOf<HTMLDivElement> = this.querySelectorAll('div.multiadd-row');
        if (children.length === 0) {
            this.appendChild(this.createNewRow(0));
        }

    }

    public connectedCallback() {
        console.log('Multiadd-connected');
    }

    private generatePrototypeNode(prototypeString: string): HTMLElement {
        let node = document.createElement('div');
        node.classList.add('multiadd-prototype')
        node.innerHTML = prototypeString;
        let children:NodeListOf<ChildNode> = node.childNodes;
        if (children.length === 0) {
            throw new Error("The prototype string could not be parsed to at least one HTMLNode");
        }
        return node;
    }

    private createNewRow(lastDelta: Number): HTMLElement {
        let newRow: HTMLElement = this.prototype.cloneNode(true) as HTMLElement;
        newRow.classList.remove('multiadd-prototype');
        newRow.classList.add('multiadd-row');
        newRow.childNodes.forEach((childNode: ChildNode) => {
            let childElement: HTMLElement = childNode as HTMLElement;
            if (childElement instanceof HTMLInputElement) {
                childElement.id = childElement.id.replace('__name__', `${lastDelta}`)
                let childElementName: string|null = childElement.getAttribute('name');
                if (childElementName !== null) {
                    childElement.setAttribute('name', childElementName.replace('__name__', `${lastDelta}`));
                }
            }
        })

        return newRow;
    }
}
