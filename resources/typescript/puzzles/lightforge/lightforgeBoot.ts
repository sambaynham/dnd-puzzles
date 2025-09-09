import LightForge from "./LightForge";
document.addEventListener('DOMContentLoaded', () => {
    window.customElements.define('lightforge-puzzle', LightForge, { extends: 'div'});
});
