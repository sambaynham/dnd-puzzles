import createClient, {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";

export class CasebookSubject extends HTMLElement {

    private readonly subjectId: string;

    private readonly instanceCode: string;

    private client: Client<paths>;

    private subjectWrapper: HTMLDivElement;

    constructor() {
        super();

        let subjectId: string|undefined = this.dataset.subjectid,
            instanceCode: string|undefined = this.dataset.instancecode,
            template: HTMLElement|null = document.getElementById("casebook-subject-template");

        if (subjectId === undefined) {
            throw new Error(`No subject found with id ${subjectId}`);
        }


        if (instanceCode === undefined) {
            throw new Error(`No instance found with id ${instanceCode}`);
        }



        if (null === template) {
            throw new Error(`No Template found`);
        }

        this.subjectId = subjectId;
        this.instanceCode = instanceCode;
        this.client = createClient<paths>({ baseUrl: "http://localhost:8089" });

        // @ts-ignore
        let templateContent = template.content;

        const shadowRoot = this.attachShadow({ mode: "open" });

        shadowRoot.appendChild(templateContent.cloneNode(true));

        const subjectWrapper: HTMLDivElement|null = shadowRoot.querySelector('div.subject-wrapper');
        if (null === subjectWrapper) {
            throw new Error('No subject wrapper element found.');
        }

        this.subjectWrapper = subjectWrapper;
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

            this.subjectWrapper.classList.add('loaded');
            this.buildClues();
        }
    }
    private async buildClues() {
        const {data, error} = await this.client.GET("/api/puzzles/static/casebook/{instanceCode}/subjects/{subjectId}/clues",
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
            console.log(error);
            throw new Error();
        }
        console.log(data);

    }
}
