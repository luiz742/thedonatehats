import axios from 'axios';
import { router } from '@inertiajs/vue3'

router.on('error', (errors) => {
    if (errors.response?.status === 401 || errors.response?.status === 419) {
        window.location.href = '/login'
    }
})

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
