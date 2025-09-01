import HammerNode from "./HammerNode";
import {NodeDetail} from "./Types/NodeDetail";
import HammerPowerLevel from "./HammerPowerLevel";
import HammerOutput from "./HammerOutput";
import {FiringDetail} from "./Types/FiringDetail";
import HammerFiringButton from "./HammerFiringButton";


export default class HammerPuzzle extends HTMLDivElement {

    private hammerPowerLevel: HammerPowerLevel;

    private hammerOutput: HammerOutput;

    private nodes: NodeListOf<HammerNode>;

    private initialValues: NodeDetail[] = [];

    constructor() {
        super();
        let hammerPowerLevel = this.querySelector('.hammer-power'),
            hammerOutput = this.querySelector('.hammer-output'),
            hammerNodes: NodeListOf<HammerNode> = this.querySelectorAll('.hammer-node');
        this.guard(hammerNodes, hammerPowerLevel, hammerOutput);
        this.nodes = hammerNodes;
        this.hammerPowerLevel = hammerPowerLevel as HammerPowerLevel;
        this.hammerOutput = hammerOutput as HammerOutput;

        this.storeInitialValues();
    }

    public connectedCallback() {
        this.setupListeners();
    }

    private setupListeners(): void {
        this.addEventListener('hammer-node-rotated', (event: CustomEventInit<object>) => {
            let detail = event.detail as NodeDetail;
            if (detail.isActive) {
                this.propagateNodeActivation(detail);
            }
            this.hammerOutput.pushMessage(`Node ${detail.id} rotated`);
            this.setPowerLevel();
        });

        this.addEventListener('hammer-node-activated', (event: CustomEventInit<object>) => {
            let detail = event.detail as NodeDetail;
            this.propagateNodeActivation(detail);
        });

        this.addEventListener('puzzle-overvolt-event', () => {
            this.resetPuzzle('Safe power levels exceeded. Resetting solution.');
        });

        this.addEventListener('puzzle-success-event', () => {
            if (this.querySelector('.hammer-node.active.broken') === null) {
                this.hammerOutput.pushMessage('Stable power levels detected. Charging.');
                this.classList.add('success');
            } else {
                this.resetPuzzle('Invalid node proposed. Resetting solution')
            }
        });

        this.addEventListener('hammer-fired-event', (e: CustomEventInit<FiringDetail>)=>  {
            let total: number = 0;
            let rollString:string = '';
            e.detail?.damage.forEach(function (rollTotal:number) {
                total+= rollTotal;
                rollString = `${rollString}${rollTotal},`
            })
            let message = `A searing beam of light strikes ${e.detail?.target}, dealing ${total} damage to everything within it. (${rollString})`;
            this.querySelectorAll('.hammer-firing-button').forEach((button: Element)=> {
                if (button instanceof HammerFiringButton) {
                    button.setAttribute('disabled', 'true');
                }
            })
            this.hammerOutput.pushMessage(message);
        });

        this.addEventListener('reset-button-clicked', ()=> {
            this.resetPuzzle('Manual reset requested');
        });

        this.addEventListener('translation-request', () => {
            console.log('translate request received');
            this.classList.toggle('translated');
        });
    }

    private guard(hammerNodes: NodeListOf<HammerNode>, hammerPowerLevel: Element|null, hammerOutput: Element|null) {
        if (hammerNodes.length === 0) {
            throw new Error("No nodes found.");
        }

        if (!(hammerPowerLevel instanceof HammerPowerLevel)) {
            throw new Error('HammerPowerLevel must be defined');
        }

        if (!(hammerOutput instanceof HammerOutput)) {
            throw new Error('HammerOutput must be defined');
        }
    }
    private propagateNodeActivation(nodeDetail: NodeDetail) {

        let activatesRotated = nodeDetail.activatesRotated;
        let activatesDefault = nodeDetail.activatesDefault;

        let activatesRotatedNode: Element|null = null;
        let activatesDefaultNode: Element|null = null;

        if (activatesDefault !== '') {
            activatesDefaultNode = this.querySelector(`.hammer-node#${nodeDetail.activatesDefault}`);
        }

        if (activatesRotated !== '') {
            activatesRotatedNode = this.querySelector(`.hammer-node#${nodeDetail.activatesRotated}`);
        }

        if (nodeDetail.isRotated) {
            if (activatesRotatedNode instanceof HammerNode) {
                if (activatesRotatedNode.dataset.active !== 'true') {
                    activatesRotatedNode.dataset.active = 'true';
                }

            }
            if (activatesDefaultNode instanceof HammerNode) {
                if (activatesDefaultNode.dataset.active !== 'false') {
                    activatesDefaultNode.dataset.active = 'false';
                }
            }
        } else {
            if (activatesRotatedNode instanceof HammerNode) {
                if (activatesRotatedNode.dataset.active !== 'false') {
                    activatesRotatedNode.dataset.active = 'false';
                }
            }
            if (activatesDefaultNode instanceof HammerNode) {
                if (activatesDefaultNode.dataset.active != 'true') {
                    activatesDefaultNode.dataset.active = 'true';
                }
            }
        }
    }

    private setPowerLevel(): void {
        let activeNodeCount: number = 0;
        this.nodes.forEach(function(node: HammerNode) {
            if (node.dataset.active === 'true') {
                activeNodeCount++;
            }
        });
        this.hammerOutput.pushMessage(`Power level is ${activeNodeCount}`);
        this.hammerPowerLevel.setAttribute('value', `${activeNodeCount}`);
    }

    private storeInitialValues() {
        this.nodes.forEach((node: HammerNode) => {
            this.initialValues.push(node.generateNodeDetails());
        });
    }

    private resetPuzzle(puzzleResetMessage: string): void {
        this.disableInput();
        this.hammerOutput.pushMessage(puzzleResetMessage);
        setTimeout(() => {

            this.initialValues.forEach((initialValue:NodeDetail) => {
                let node = this.querySelector(`.hammer-node#${initialValue.id}`);
                if (node instanceof HammerNode) {
                    node.dataset.active = initialValue.isActive ? 'true' : 'false';
                }
            });
            this.nodes.forEach((node: HammerNode) => {
                node.dataset.rotated = 'false';
                node.classList.remove('rotated');
            });
            this.classList.remove('success');
            this.setPowerLevel();
            this.enableInput();
        }, 1500);
    }

    private disableInput(): void {
        this.nodes.forEach((node: HammerNode) => {
            node.setAttribute('disabled', 'true');
        })
    }

    private enableInput(): void {
        this.nodes.forEach((node: HammerNode) => {
            node.removeAttribute('disabled');
        })
    }
}
