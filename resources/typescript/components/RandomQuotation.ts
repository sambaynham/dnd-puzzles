import createClient, {Client} from "openapi-fetch";
import type { paths } from "../schema/schema";

export default class RandomQuotation extends HTMLQuoteElement {

    private client: Client<paths>;
    private quotationElement: HTMLParagraphElement;
    private citationElement: HTMLSpanElement;

    constructor() {
        super();
        this.client = createClient<paths>({ baseUrl: "http://localhost:8089" });
        let quotationElement: HTMLParagraphElement = document.createElement('p');
        let citationElement: HTMLSpanElement = document.createElement('span');
        quotationElement.classList.add('quotation');
        citationElement.classList.add('cite');

        this.quotationElement = quotationElement;
        this.citationElement = citationElement;
        this.appendChild(quotationElement);
        this.appendChild(citationElement);
    }

    connectedCallback(): void {
        this.getQuote();

        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            this.getQuote();
        })
    }

    async getQuote(): Promise<void> {
        this.classList.add('loading');
        const { data, error } = await this.client.GET("/api/quote", {});

        if (error) {
            console.error(error);
        } else {
            this.quotationElement.innerHTML = data.quote ?? '';
            this.citationElement.innerHTML = data.citation ?? '';
            this.classList.remove('loading');
        }
    }
}
