

import ProgressBar from "./components/ProgressBar";
import NavToggle from "./components/NavToggle";
import ToolTip from "./components/Tooltip";
import MessageOfTheDay from "./components/MessageOfTheDay";
import {FlashAlert} from "./components/FlashAlert";
import {initialiseDropCaps} from "./behaviours/dropCap";
import {SlideShow} from "./components/SlideShow/SlideShow";
import AccordionList from "./components/Accordion/AccordionList";
import {AccordionEntry} from "./components/Accordion/AccordionEntry";


(()=> {
    document.addEventListener('DOMContentLoaded', () => {
        const body: HTMLBodyElement|null = document.querySelector('body');

        if (null === body) {
            throw new Error('Body not found');
        }
        body.classList.add('loaded');
        initialiseCustomElements();
        initialiseDropCaps();

    });

    function initialiseCustomElements(): void {
        let customElementRegistry: CustomElementRegistry = window.customElements;

        customElementRegistry.define('progress-bar', ProgressBar);
        customElementRegistry.define('nav-toggle', NavToggle, { extends: 'button'});
        customElementRegistry.define('tooltip-element', ToolTip, {extends: 'span'});
        customElementRegistry.define('motd-span', MessageOfTheDay, {extends: 'span'})
        customElementRegistry.define('flash-alert', FlashAlert, {extends: 'span'});
        customElementRegistry.define('slideshow-element', SlideShow, {extends: 'div'});
        customElementRegistry.define('accordion-list', AccordionList, {extends: 'div'});
        customElementRegistry.define('accordion-entry', AccordionEntry, {extends: 'div'});
    }
})();



