export class CasebookSubjectClue extends HTMLDivElement {
    constructor() {
        super();
    }

    connectedCallback() {
        console.log('CasebookSubjectClue connected');
        console.log(this.dataset.clueid);

    }
}
