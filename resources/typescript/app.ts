
import '../sass/app.scss';
import DropCap from "./components/dropCap";
import ProgressBar from "./components/ProgressBar";

document.addEventListener('DOMContentLoaded', () => {
    customElements.define('drop-cap', DropCap, {extends: 'p'})
    customElements.define('progress-bar', ProgressBar)
});
