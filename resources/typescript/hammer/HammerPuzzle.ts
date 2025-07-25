import HammerNode from "./HammerNode";
import {NodeDetail} from "./Types/NodeDetail";

export default class HammerPuzzle extends HTMLDivElement {
    constructor() {
        super();
    }

    public connectedCallback() {


        this.addEventListener('hammer-node-rotated', (e: CustomEventInit<object>) => {
            if (e.detail != undefined) {
                let detail  = e.detail as NodeDetail;
                let nodeToActivate:HammerNode|null = this.querySelector(`hammer-node#${detail.nodeToActivate}`);
                let nodeToDeactivate:HammerNode|null = this.querySelector(`hammer-node#${detail.nodeToDeactivate}`);
                if (nodeToActivate != null ) {
                    nodeToActivate.dataset.active = 'true';
                }
                if (nodeToDeactivate != null) {
                    nodeToDeactivate.dataset.active = 'false';
                }
            }
            if(this.assessSuccess()) {
                this.classList.add('success');
            }

        });
    }

    private assessSuccess(): boolean {



        const successNodes: HammerNode[] = [
            this.querySelector('hammer-node#two') as HammerNode,
            this.querySelector('hammer-node#four') as HammerNode,
            this.querySelector('hammer-node#six') as HammerNode,
            this.querySelector('hammer-node#eight') as HammerNode,
        ];
        let success = true;

        successNodes.forEach(node => {
            if (node.dataset.active !== 'true') {
                success = false;
            }
        })
        return success;
    }
}
