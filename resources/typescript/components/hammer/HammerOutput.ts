export default class HammerOutput extends HTMLUListElement {

    private messages: string[] = [];
    public constructor() {
        super();
    }
    public pushMessage(message: string): void {
        this.messages.push(message);
        let liNode: HTMLLIElement = document.createElement('li');
        liNode.innerHTML = message;
        this.appendChild(liNode);
        this.scrollTop = this.scrollHeight;
    }
}
