import { defineStore } from 'pinia';
import axios from '@/bootstrap';

const URL = import.meta.env.VITE_API_APP_URL;

export const useFileStore = defineStore('files', () => {
    function getFiles() {
        return axios.get(URL + 'files/');
    }

    function uploadFile(parent, data) {
        return axios.post(URL + 'files/', {
            parent_id: parent.value,
            name: data.name,
            type: data.type,
        });
    }
    return {
        getFiles,
        uploadFile,
    };
});