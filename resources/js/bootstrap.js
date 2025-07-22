import axios from 'axios';
import Alpine from 'alpinejs'
import Sortable from 'sortablejs';

window.axios = axios;
window.Sortable = Sortable;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {
    window.Alpine = Alpine;
    Alpine.start();
});