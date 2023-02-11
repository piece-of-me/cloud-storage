import axios from 'axios';
import { useSessionStorage } from '@vueuse/core';

const sessionStorage = useSessionStorage('userToken', null);

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

if (sessionStorage.value !== null) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + sessionStorage.value;
}

export default axios;