import {CasebookSubject} from "./CasebookSubject";

document.addEventListener('DOMContentLoaded', () => {

    let customElementRegistry = window.customElements;
    customElementRegistry.define('casebook-subject', CasebookSubject, { extends: 'article'});

});
