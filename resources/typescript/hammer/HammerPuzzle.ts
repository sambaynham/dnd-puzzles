export default class HammerPuzzle extends HTMLDivElement {
    constructor() {
        super();
    }

    public connectedCallback() {
        console.log('Stop, hammer time.');
        this.addEventListener('hammer-node-rotated', (e: CustomEventInit<object>) => {
            console.log(e.detail);
        });

    }
}
