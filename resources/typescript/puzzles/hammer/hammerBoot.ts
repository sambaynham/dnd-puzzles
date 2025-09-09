import HammerNode from "./HammerNode";
import HammerFiringButton from "./HammerFiringButton";
import HammerTranslateButton from "./HammerTranslateButton";
import HammerResetButton from "./HammerResetButton";
import HammerPowerLevel from "./HammerPowerLevel";
import HammerOutput from "./HammerOutput";
import HammerPuzzle from "./HammerPuzzle";

document.addEventListener('DOMContentLoaded', () => {
    let customElementRegistry = window.customElements;
    customElementRegistry.define('hammer-node', HammerNode, { extends: 'button'});
    customElementRegistry.define('hammer-firing-button', HammerFiringButton, { extends: 'button'});
    customElementRegistry.define('hammer-translate-button', HammerTranslateButton, { extends: 'button'});
    customElementRegistry.define('hammer-reset-button', HammerResetButton, { extends: 'button'});
    customElementRegistry.define('hammer-power', HammerPowerLevel, { extends: 'meter'});
    customElementRegistry.define('hammer-output', HammerOutput, { extends: 'ul'});
    customElementRegistry.define('hammer-puzzle', HammerPuzzle, { extends: 'div'});
});
