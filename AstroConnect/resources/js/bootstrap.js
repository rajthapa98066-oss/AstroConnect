// Expose axios globally for AJAX requests from Blade scripts.
import axios from 'axios';
window.axios = axios;

// Mark requests as XMLHttpRequest for Laravel request handling.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
