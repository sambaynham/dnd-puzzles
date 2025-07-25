
import '../sass/app.scss';
import DropCap from "./components/dropCap";
import ProgressBar from "./components/ProgressBar";
import HammerPuzzle from "./hammer/HammerPuzzle";
import HammerNode from "./hammer/HammerNode";

document.addEventListener('DOMContentLoaded', () => {
    customElements.define('drop-cap', DropCap, {extends: 'p'})
    customElements.define('progress-bar', ProgressBar)
    customElements.define('hammer-puzzle', HammerPuzzle, { extends: 'div'})
    customElements.define('hammer-node', HammerNode, { extends: 'button'})
});
