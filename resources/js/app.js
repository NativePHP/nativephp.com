import './bootstrap';
import Alpine from 'alpinejs'
import codeBlock from "./alpine/codeBlock.js";

window.Alpine = Alpine;

Alpine.data('codeBlock', codeBlock)
Alpine.start()
