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

        const shadowRoot = this.attachShadow({ mode: "open" });
        shadowRoot.appendChild(document.importNode(templateContent, true));

    }

    connectedCallback() {
        this.buildContent()
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
            if (data.imageUri) {
                let imageElement = document.createElement("img");
                imageElement.src = `/uploads/images/${data.imageUri}`;
                imageElement.alt = data.name;
                imageElement.classList.add('card-backer');
                this.append(imageElement);
            }

                let titleElement = document.createElement("h2");
                titleElement.textContent = data.name;
                titleElement.classList.add('card-title');
                this.append(titleElement);

                let cardBodyElement = document.createElement("div");
                cardBodyElement.classList.add('card-body');
                cardBodyElement.innerHTML = data.description;
                this.append(cardBodyElement);
        }
    }
}
