import './bootstrap';
import './theme';
import { renderIcons } from './icons';
import { registerServiceWorker } from './sw-register';
import { initOfflineIndicator } from './offline-indicator';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();

renderIcons();
document.addEventListener('livewire:navigated', renderIcons);
registerServiceWorker();
initOfflineIndicator();
