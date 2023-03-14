<script>
export default {
  name: 'PublicFile',
};
</script>

<script setup>
import logoUrl from 'images/logo.png';
import { ref, reactive, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElLoading, ElMessageBox, ElMessage } from 'element-plus';
import { getFileLogoUrl } from '@/utils/extensions.js';
import { useUserStore } from '@/store/user.store.js';
import { useFileStore } from '@/store/file.store.js';
const $route = useRoute();
const $router = useRouter();
const { files, publicDownload: download, publicGetFiles: getFiles, saveToStorage } = useFileStore();
const { authenticated } = useUserStore();

let root = ref({});
let file = ref({});
let requestedFileType = ref();
const fileInfo = reactive({
  visible: false,
  file: {},
});
const openFolders = ref([]);
const selectedFiles = computed(() => {
  const selectedFolder = openFolders.value.length <= 0 ? root.value.id : openFolders.value[openFolders.value.length - 1];
  return files.data
    .filter(file => file?.parentId === selectedFolder?.id || selectedFolder === root.value.id && file?.parentId === root.value.id)
    .map(file => {
      file.shortName = file.name.length <= 25 ? file.name : file.name.slice(0, 12).trim() + ' . . . ' + file.name.slice(-12).trim();
      return file;
    });
});

const clickHandler = {
  type: '',
  leftClick(file) {
    this.type = 'leftClick';
    setTimeout(() => {
      if (this.type !== 'leftClick') return;
      fileInfo.visible = true;
      fileInfo.file = file;
      this.type = '';
    }, 300);
  },
  dbClick(file) {
    this.type = 'dbClick';
    if (file.typeId === 2) {
      fileInfo.visible = false;
      fileInfo.file = {};
      openFolders.value.push(file);
    }
    this.type = '';
  },
};

function goToFolder(folder) {
  if (folder === null) {
    openFolders.value = [];
  }
  const selectedFolderIndex = openFolders.value.findIndex(file => file?.id === folder.id);
  openFolders.value = openFolders.value.slice(0, selectedFolderIndex + 1);
}

function downloadFile(file) {
  const loading = ElLoading.service({
    lock: true,
    text: 'Скачивание файла',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  download(file).catch(() => {
    ElMessageBox.alert('Произошла ошибка при скачивании файла', 'Ошибка', {
      showConfirmButton: false,
      showClose: false,
      closeOnClickModal: true,
      closeOnPressEscape: true,
      center: true,
    });
  }).finally(() => {
    loading.close();
  });
}

function save(file) {
  const loading = ElLoading.service({
    lock: true,
    text: 'Сохранение',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  saveToStorage(file).then(() => {
    ElMessage({
      message: file.typeId === 2 ? 'Папка успешно сохранена' : 'Файл успешно сохранен',
      type: 'success',
    })
  }).finally(() => {
    loading.close();
  }).catch(error => {
    const message = error.response.data?.message ?? 'Произошла ошибка при сохранении файла';
    ElMessageBox.alert(message, 'Ошибка', {
      showConfirmButton: false,
      showClose: false,
      closeOnClickModal: true,
      closeOnPressEscape: true,
      center: true,
    });
  });
}

onMounted(() => {
  const loading = ElLoading.service({
    lock: true,
    text: 'Загрузка данных',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  getFiles($route.params.hash).then(response => {
    if (!response?.data?.file) {
      throw new Error();
    }
    if (response.data.hasOwnProperty('files')) {
      requestedFileType.value = 2;
      root.value = response.data.file;
    } else {
      requestedFileType.value = response.data.file.typeId;
      file.value = response.data.file;
    }
  }).catch(() => {
    ElMessageBox.confirm('Ошибка при загрузке файлов', 'Ошибка', {
      showClose: false,
      showCancelButton: false,
      confirmButtonText: 'OK',
    }).then(() => {
      $router.replace({ name: 'main' });
    });
  }).finally(() => {
    loading.close();
  });
});

const tableData = computed(() => {
  const [date, time] = file.value.hasOwnProperty('updatedAt') ? file.value.updatedAt.split(' ') : [];
  return [{
    name: file.value?.name,
    date,
    time
  }];
});
</script>

<template>
  <div class="bg-stone-200 w-screen h-screen">
    <el-container>
      <el-header class="flex flex-row justify-start mx-2">
        <div class="flex flex-row items-center w-1/5">
          <el-image :src="logoUrl" class="w-20"/>
          <p class="text-3xl">Storage</p>
        </div>
        <div :class="['bg-stone-600 h-full rounded-b-lg w-2/4 px-5 flex items-center justify-between', { hidden: !fileInfo.visible }]">
          <div class="mr-3 text-white font-bold w-3/6">
            <el-popover
              placement="bottom"
              :width="200"
              trigger="hover"
            >
              <template #reference>
                <el-icon><QuestionFilled /></el-icon>
              </template>
              <p><strong>Размер:</strong> {{ fileInfo.file.sizeStr }}</p>
            </el-popover>
            {{ fileInfo.file.shortName }}
          </div>
          <div>
            <el-button @click="save(fileInfo.file)" v-if="authenticated"><el-icon class="mr-2"><Plus /></el-icon> Сохранить себе</el-button>
            <el-button @click="downloadFile(fileInfo.file)"><el-icon class="mr-2"><Download /></el-icon> Скачать</el-button>
            <el-button icon="CloseBold" circle @click="fileInfo.visible = false"/>
          </div>
        </div>
      </el-header>

      <el-container class="bg-white rounded-2xl mx-5 mt-3">
        <div class="folder-content w-full" v-if="requestedFileType === 2">
          <div class="pl-8 pr-8 pt-6">
            <div class="flex justify-between">
              <div>
                <el-breadcrumb :separator-icon="ArrowRight">
                  <el-breadcrumb-item class="text-xl cursor-pointer">
                    <span class="font-semibold" @click="goToFolder(root)"> {{ root.name }}</span>
                  </el-breadcrumb-item>

                  <el-breadcrumb-item
                    v-for="folder in openFolders"
                    class="text-xl cursor-pointer"
                  >
                    <span class="font-semibold" @click="goToFolder(folder)">{{ folder.name }}</span>
                  </el-breadcrumb-item>
                </el-breadcrumb>
              </div>
              <div>
                <el-button type="primary" @click="save(root)" v-if="authenticated"><el-icon class="mr-2"><Plus /></el-icon> Сохранить все в хранилище</el-button>
                <el-button type="primary" @click="downloadFile(root)"><el-icon class="mr-2"><Download /></el-icon> Скачать все</el-button>
              </div>
            </div>
          </div>
          <el-main>
            <div
              class="flex flex-row flex-wrap"
              v-loading="!files.loading"
            >
              <div
                v-if="files.loading && files.data.length"
                class="flex flex-wrap"
              >
                <div
                  class="w-32 h-max hover:bg-stone-100 hover:cursor-pointer rounded-2xl pb-3 mr-3"
                  v-for="file in selectedFiles"
                  @click.left="clickHandler.leftClick(file)"
                  @dblclick="clickHandler.dbClick(file)"
                >
                  <div class="relative h-36">
                    <el-image :src="getFileLogoUrl(file)" class="w-full h-32" fit="contain"/>
                    <el-tooltip
                      class="box-item"
                      effect="dark"
                      placement="right"
                      v-if="file.public"
                    >
                      <template #content>
                        <p class="font-bold text-sm">
                          <el-icon>
                            <View/>
                          </el-icon>
                          {{ file.views }}
                          <el-icon>
                            <Download/>
                          </el-icon>
                          {{ file.downloads }}
                        </p>
                      </template>
                      <el-button type="info" icon="Link" circle class="absolute right-0 bottom-4"/>
                    </el-tooltip>

                  </div>
                  <p class="text-center tracking-narrowly leading-4">{{ file.shortName }}</p>
                </div>
              </div>
              <div
                v-else-if="files.loading && files.data.length <= 0"
                class="flex justify-center w-full"
              >
                <el-empty>
                </el-empty>
              </div>
            </div>
          </el-main>
        </div>
        <div class="p-8 w-full" v-else>
          <el-table :data="tableData" class="w-full" v-show="files.loading">
            <el-table-column prop="name" label="Название файла" />
            <el-table-column prop="date" label="Дата добавления" width="240px" />
            <el-table-column prop="time" label="Время добавления" width="240px" />
            <el-table-column :width="authenticated ? '260px' : '140px'">
              <template #default="scope">
                <div class="flex flex-row">
                  <el-button @click="save(file)" v-if="authenticated"><el-icon class="mr-2"><Plus /></el-icon> Сохранить себе</el-button>
                  <el-button @click="downloadFile(file)" type="primary"><el-icon><Download /></el-icon></el-button>
                </div>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-container>
    </el-container>
  </div>
</template>

<style scoped>

</style>