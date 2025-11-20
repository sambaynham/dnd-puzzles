import RunicSwitches from './switches/RunicSwitches';
import AlchemicalPotions from './potions/AlchemicalPotions';
import ArcaneRings from './rings/ArcaneRings';

document.addEventListener('DOMContentLoaded', () => {
    const registry = window.customElements;

    registry.define('runic-switches', RunicSwitches, { extends: 'div' });
    registry.define('alchemical-potions', AlchemicalPotions, { extends: 'div' });
    registry.define('arcane-rings', ArcaneRings, { extends: 'div' });
});
