export class PostButton extends HTMLButtonElement {

    private csrfToken: string;

    private uri: string;
    constructor() {
        super();

        if (this.dataset.csrftoken === undefined) {
            throw new Error('CSRF Token must be defined');
        }

        if (this.dataset.uri === undefined) {
            throw new Error('URI must be defined');
        }

        this.csrfToken = this.dataset.csrftoken;
        this.uri = this.dataset.uri;
    }

    connectedCallback(): void {
        this.setupListeners();
    }

    private setupListeners(): void {
        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            this.post();
        })
    }

    private async post() {
        console.log(this.csrfToken);
        const response = await fetch(this.uri, {
            method: "POST",
        });
        if (response.ok) {
            this.setAttribute('disabled', 'true');
        }
    }
}
