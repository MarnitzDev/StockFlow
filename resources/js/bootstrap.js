import axios from 'axios'

window.axios = axios

// ✅ This forces HTTPS if the page is served over HTTPS
window.axios.defaults.baseURL = window.location.origin

// Required headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
