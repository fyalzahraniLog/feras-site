import { initBlackhole } from './blackhole';

initBlackhole();

document.addEventListener('livewire:navigated', () => initBlackhole());
