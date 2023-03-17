import axios from 'axios';

const token = localStorage.getItem('user-token');

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

if (token !== null) {
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

export default axios;
