import { defineStore } from 'pinia';
import axios from '@/bootstrap';
import { ref, readonly } from 'vue';
import { useSessionStorage } from "@vueuse/core";

const sessionStorage = useSessionStorage('userToken', null);
const URL = import.meta.env.VITE_API_APP_URL;

export const useUserStore = defineStore('users', () => {
    let authenticated = ref(sessionStorage.value !== null);

    async function login(email, password) {
        const request = await axios.get('/sanctum/csrf-cookie');
        if (request.status && request.status === 204) {
            return axios.post(URL + 'auth/login', {
                email,
                password,
            }).then(response => {
                if (response.status !== 200 || !response.headers.hasOwnProperty('token')) return;
                sessionStorage.value = response.headers.token;
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.headers.token;
                authenticated.value = true;
            });
        } else {
            return new Promise((resolve, reject) => {
                reject();
            });
        }
    }

    function register(email) {
        return axios.post(URL + 'auth/register', {
            email,
        });
    }

    function resetPassword(email) {
        return axios.post(URL + 'auth/reset', {
            email,
        });
    }
    return {
        authenticated: readonly(authenticated),
        login,
        register,
        resetPassword,
    };
});