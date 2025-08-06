import axios from 'axios'

window.axios = axios

// Required for Laravel
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Enable sending credentials with cross-domain requests
window.axios.defaults.withCredentials = true;

// Enable automatic XSRF-TOKEN handling
window.axios.defaults.withXSRFToken = true;

// Get CSRF from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found')
}
