import { defineStore } from 'pinia';
import axios from '@/bootstrap';
import { ref, readonly } from 'vue';
import { useSessionStorage } from '@vueuse/core';

const userToken = useSessionStorage('userToken', null);
const URL = import.meta.env.VITE_API_APP_URL;

export const useUserStore = defineStore('users', () => {
    let authenticated = ref(userToken.value !== null);

    async function login(email, password) {
        const request = await axios.get('/sanctum/csrf-cookie');
        if (request.status && request.status === 204) {
            return axios.post(URL + 'auth/login', {
                email,
                password,
            }).then(response => {
                if (response.status !== 200 || !response.headers.hasOwnProperty('token')) return;
                userToken.value = response.headers.token;
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

    function logout() {
        return axios.post(URL + 'auth/logout').then(response => {
            if (response.status !== 200) return;
            userToken.value = null;
            authenticated.value = false;
            delete (axios.defaults.headers.common['Authorization']);
        });
    }

    return {
        authenticated: readonly(authenticated),
        login,
        register,
        resetPassword,
        logout,
    };
});