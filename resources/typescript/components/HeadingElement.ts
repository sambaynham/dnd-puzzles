export class HeadingElement extends HTMLElement {
    connectedCallback() {
        if (this instanceof HTMLHeadingElement) {
            throw new Error("Only HTMLHeading Elements can use this component.");
        }
        this.classList.add("heading-element");
        this.dataset.headingcontent = this.innerText;
    }
}
