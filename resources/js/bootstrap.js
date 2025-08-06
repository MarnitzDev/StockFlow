import axios from 'axios'

window.axios = axios

// Required for Laravel
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Get CSRF from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]')

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found')
}
