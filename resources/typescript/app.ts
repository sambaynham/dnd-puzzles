
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
    let customElementRegistry = window.customElements;

    customElementRegistry.define('drop-cap', DropCap, {extends: 'p'})
    customElementRegistry.define('progress-bar', ProgressBar)
    customElementRegistry.define('hammer-node', HammerNode, { extends: 'button'})
    customElementRegistry.define('hammer-firing-button', HammerFiringButton, { extends: 'button'})
    customElementRegistry.define('hammer-translate-button', HammerTranslateButton, { extends: 'button'})
    customElementRegistry.define('hammer-reset-button', HammerResetButton, { extends: 'button'})
    customElementRegistry.define('hammer-power', HammerPowerLevel, { extends: 'meter'})
    customElementRegistry.define('hammer-output', HammerOutput, { extends: 'ul'})
    customElementRegistry.define('hammer-puzzle', HammerPuzzle, { extends: 'div'})
});
