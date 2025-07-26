
import '../sass/app.scss';
import DropCap from "./components/dropCap";
import ProgressBar from "./components/ProgressBar";
import HammerPuzzle from "./components/hammer/HammerPuzzle";
import HammerNode from "./components/hammer/HammerNode";
import HammerPowerLevel from "./components/hammer/HammerPowerLevel";
import HammerOutput from "./components/hammer/HammerOutput";
import HammerFiringButton from "./components/hammer/HammerFiringButton";
import HammerResetButton from "./components/hammer/HammerResetButton";
import HammerTranslateButton from "./components/hammer/HammerTranslateButton";

document.addEventListener('DOMContentLoaded', () => {
    customElements.define('drop-cap', DropCap, {extends: 'p'})
    customElements.define('progress-bar', ProgressBar)
    customElements.define('hammer-node', HammerNode, { extends: 'button'})
    customElements.define('hammer-firing-button', HammerFiringButton, { extends: 'button'})
    customElements.define('hammer-translate-button', HammerTranslateButton, { extends: 'button'})
    customElements.define('hammer-reset-button', HammerResetButton, { extends: 'button'})
    customElements.define('hammer-power', HammerPowerLevel, { extends: 'meter'})
    customElements.define('hammer-output', HammerOutput, { extends: 'ul'})
    customElements.define('hammer-puzzle', HammerPuzzle, { extends: 'div'})
});
