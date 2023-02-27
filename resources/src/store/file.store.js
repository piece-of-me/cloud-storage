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

    function getFoldersByParentId(parentId) {
        return files.data.filter(file => file.parentId === parentId && file.typeId === 2);
    }

    function getFiles() {
        return axios.get(URL + 'files/').then(response => {
            if (response.status === 200 && response.hasOwnProperty('data')) {
                files.loading = true;
                files.data = response.data;
            }
            return response;
        }).catch(() => {
            alert('Ошибка загрузки файлов');
        });
    }

    function createFolder(parent, name) {
        const id = parent ? parent.id : null;
        return axios.post(URL + 'files/create', {
            parent_id: id,
            name: name,
        }).then(response => {
            if (response.status === 200 && response.data?.folder) {
                files.data.push(response.data.folder);
            }
            return response;
        });
    }

    function uploadFile(parent, data) {
        const id = parent ? parent.id : null;
        return axios.post(URL + 'files/upload', {
            parent_id: id,
            ...data,
        }, {
            headers: {'Content-Type': 'multipart/form-data'}
        }).then(response => {
            if (response.status === 200) {
                if (response.data?.file) {
                    files.data.push(response.data.file);
                }
                if (response.data?.updatedFolders) {
                    files.data = updateFoldersInfo(response.data.updatedFolders);
                }
            }
            return response;
        });
    }

    function renameFile(id, name) {
        return axios.patch(`${URL}files/${id}/rename`, {
            name,
        }).then(response => {
            const file = response.data.file;
            for (let i in files.data) {
                if (files.data[i].id === file.id) {
                    files.data[i] = file;
                    return;
                }
            }
            return response;
        });
    }

    function moveFile(id, newParentId) {
        const parent = newParentId ? newParentId : '';
        return axios.patch(`${URL}files/${id}/move/${parent}`).then(response => {
            if (response.status === 200) {
                if (response.data?.file) {
                    const file = response.data.file;
                    for (let i in files.data) {
                        if (files.data[i].id === file.id) {
                            files.data[i] = file;
                            break;
                        }
                    }
                }
                if (response.data?.updatedFolders) {
                    files.data = updateFoldersInfo(response.data.updatedFolders);
                }
            }
            return response;
        });
    }

    function copyFile(id, parentId) {
        const parent = parentId ? parentId : '';
        return axios.patch(`${URL}files/${id}/copy/${parent}`).then(response => {
            if (response.status === 200) {
                if (response.data?.copiedFiles) {
                    response.data.copiedFiles.forEach(file => {
                        files.data.push(file);
                    });
                }
                if (response.data?.updatedFiles) {
                    files.data = updateFoldersInfo(response.data.updatedFiles);
                }
            }
            return response;
        });
    }

    function deleteFile(id) {
        return axios.delete(`${URL}files/${id}/delete`).then(response => {
            if (response.status === 200) {
                files.data = files.data.filter(file => file.id !== id);
                if (response.data?.updatedFolders) {
                    files.data = updateFoldersInfo(response.data.updatedFolders);
                }
            }
            return response;
        });
    }

    function updateFoldersInfo(updatedFolders) {
        const parentFoldersInfo = updatedFolders.reduceRight((res, currFolder) => {
            res[currFolder.id] = {
                size: currFolder.size,
                updatedAt: currFolder.updatedAt,
            };
            return res;
        }, {});
        return files.data.map(file => {
            if (parentFoldersInfo.hasOwnProperty(file.id)) {
                file.size = parentFoldersInfo[file.id].size;
                file.updatedAt = parentFoldersInfo[file.id].updatedAt;
            }
            return file;
        });
    }

    return {
        files,
        getFoldersByParentId,
        getFiles,
        createFolder,
        uploadFile,
        renameFile,
        moveFile,
        copyFile,
        deleteFile,
    };
});