import axios from 'axios';

// Set axios as a global window property if you want
window.axios = axios;

// Set the baseURL explicitly using environment variable or fallback to current origin
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL || window.location.origin;

// Set common headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
