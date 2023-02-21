import { defineStore } from 'pinia';
import axios from '@/bootstrap';
import { reactive } from 'vue';

const URL = import.meta.env.VITE_API_APP_URL;

export const useFileStore = defineStore('files', () => {
    const files = reactive({
        data: [],
        loading: false,
        error: '',
    });
    function getFiles() {
        return axios.get(URL + 'files/').then(response => {
            if (response.status === 200 && response.hasOwnProperty('data')) {
                files.loading = true;
                files.data = response.data;
            }
            return response;
        }).catch(error => {
            alert('Ошибка загрузки файлов');
        });
    }

    function uploadFile(parent, data) {
        const id = parent ? parent.id : null;
        return axios.post(URL + 'files/', {
            parent_id: id,
            ...data,
        }, {
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(response => {
            if (response.status === 200 && response.data?.data) {
                files.data.push(response.data.data);
            }
            return response;
        });
    }
    return {
        files,
        getFiles,
        uploadFile,
    };
});