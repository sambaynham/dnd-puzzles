export class FeatIcon extends HTMLDivElement {

    private readonly description: string;
    private readonly name: string;
    private readonly overlayMode: boolean = false;

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

    }
}
