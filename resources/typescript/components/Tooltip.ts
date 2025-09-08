export default class ToolTip extends HTMLSpanElement {



    private contentPanel: HTMLDivElement
    constructor() {
        super();
        if (this.title === null || this.title === '') {
            throw new Error('Title must be populated');
        }
        this.contentPanel = document.createElement('div');
        this.contentPanel.innerHTML = this.title;
        this.contentPanel.classList.add('tooltip-content');
        this.appendChild(this.contentPanel);
    }

    connectedCallback(): void {
        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();

            document.querySelectorAll('span.tooltip').forEach((toolTip) => {
                if (toolTip !== this) {
                    toolTip.classList.remove('show');
                }
            });
            this.classList.toggle('show');
        });
    }
}
