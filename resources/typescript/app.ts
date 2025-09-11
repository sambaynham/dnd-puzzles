
import DropCap from "./components/dropCap";
import ProgressBar from "./components/ProgressBar";
import NavToggle from "./components/NavToggle";
import ToolTip from "./components/Tooltip";
import MessageOfTheDay from "./components/MessageOfTheDay";


document.addEventListener('DOMContentLoaded', () => {
    const body: HTMLBodyElement|null = document.querySelector('body');

    let customElementRegistry = window.customElements;

    customElementRegistry.define('drop-cap', DropCap, {extends: 'p'});
    customElementRegistry.define('progress-bar', ProgressBar);
    customElementRegistry.define('nav-toggle', NavToggle, { extends: 'button'});
    customElementRegistry.define('tooltip-element', ToolTip, {extends: 'span'});
    customElementRegistry.define('motd-span', MessageOfTheDay, {extends: 'span'})
    if (null === body) {
        throw new Error('Body not found');
    }

    body.classList.add('loaded');
});
