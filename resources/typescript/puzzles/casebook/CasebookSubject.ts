import createClient, {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";


export class CasebookSubject extends HTMLElement {

    private subjectId: string;

    private instanceCode: string;

    private client: Client<paths>;

    constructor() {
        super();
        this.client = createClient<paths>({ baseUrl: "http://localhost:8089" });
        let subjectId: string|undefined = this.dataset.subjectid;
        if (subjectId === undefined) {
            throw new Error(`No subject found with id ${subjectId}`);
        }

        let instanceCode: string|undefined = this.dataset.instancecode;
        if (instanceCode === undefined) {
            throw new Error(`No instance found with id ${instanceCode}`);
        }

        this.subjectId = subjectId;
        this.instanceCode = instanceCode;

        let template: HTMLElement|null = document.getElementById("casebook-subject-template");
        if (null === template) {
            throw new Error(`No Template found`);
        }

        // @ts-ignore
        let templateContent = template.content;
        const sheet = new CSSStyleSheet();

        sheet.replaceSync(template.style.all);
        const shadowRoot = this.attachShadow({ mode: "open" });
        shadowRoot.adoptedStyleSheets.push(sheet);


        shadowRoot.appendChild(templateContent.cloneNode(true));


    }

    connectedCallback() {
        this.buildContent();
    }

    private async buildContent() {

        const { data, error } = await this.client.GET("/api/puzzles/static/casebook/{instanceCode}/subjects/{subjectId}",
            {
                params: {
                    path: {
                        instanceCode: this.instanceCode,
                        subjectId: this.subjectId,
                    },
                },
            }
        );
        if (error !== undefined) {
            throw new Error();
        }

        /**<img class="card-backer" src="/uploads/images/{{ subject.getCasebookSubjectImage }}" alt="{{ subject.getName }}">*/

        if (data.name && data.description) {

            const titleElement: HTMLHeadingElement|null|undefined = this.shadowRoot?.querySelector('h2[slot="subject-name"]');
            const cardBodyElement: HTMLDivElement|null|undefined = this.shadowRoot?.querySelector('slot[name="subject-description"]');

            if (titleElement === null || titleElement === undefined) {
                throw new Error('Missing title element');

            }

            if (cardBodyElement === null || cardBodyElement === undefined) {
                throw new Error('Missing body element');
            }

            if (data.imageUri) {
                const imageElement: HTMLImageElement|null|undefined = this.shadowRoot?.querySelector('img[slot="image"]');

                if (imageElement === null || imageElement === undefined) {
                    throw new Error("Image URL defined but image element missing.")
                }
                imageElement.src = `/uploads/images/${data.imageUri}`;
                imageElement.alt = data.name;
            }

            titleElement.textContent = data.name;
            cardBodyElement.innerHTML = data.description;

            this.classList.add('loaded');
        }
    }
}
