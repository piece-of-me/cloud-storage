<script setup>
import logoUrl from 'images/logo.png';
import MainWorkingPlace from '@/components/MainWorkingPlace.vue';
import MainFileInfoBlock from '@/components/MainFileInfoBlock.vue';
import { useFileStore } from '@/store/file.store.js';
import { useUserStore } from '@/store/user.store';
import { reactive, computed, ref, onMounted } from 'vue';
import { ElLoading, ElMessageBox } from 'element-plus';
import { useRouter } from 'vue-router';

const { files, getFiles, createFolder: create, uploadFile } = useFileStore();
const { logout } = useUserStore();
const $router = useRouter();

const openFolders = ref([]);
const selectedFiles = computed(() => {
  const selectedFolder = openFolders.value.length <= 0 ? null : openFolders.value[openFolders.value.length - 1];
  return files.data
    .filter(file => file?.parentId === selectedFolder?.id || selectedFolder === null && file?.parentId === null)
    .map(file => {
      file.shortName = file.name.length <= 25 ? file.name : file.name.slice(0, 12).trim() + ' . . . ' + file.name.slice(-12).trim();
      return file;
    });
});
let folderName = ref('');
let uploadRef = ref(null);
const fileInfo = reactive({
  visible: false,
  file: {},
});

const Dialog = reactive({
  visible: false,
  process: false,
  show() {
    this.visible = true;
  },
  hide() {
    this.visible = false;
  },
});

function createFolder() {
  const parentFolder = openFolders.value.length > 0 ? openFolders.value.at(-1) : null;
  Dialog.process = true;
  create(parentFolder, folderName.value).catch(response => {
    const message = response?.response?.data?.message ?? 'Возникла ошибка в ходе выполнения запроса';
    ElMessageBox.confirm(message, 'Ошибка', {
      confirmButtonText: 'Ок',
      type: 'error',
      center: true,
      showCancelButton: false,
      showClose: false
    });
  }).finally(() => {
    folderName.value = '';
    Dialog.hide();
    Dialog.process = false;
  });
}

function exit() {
  logout().then(() => {
    $router.replace({name: 'login'});
  });
}

function open(folder) {
  fileInfo.visible = false;
  fileInfo.file = {};
  openFolders.value.push(folder);
}
function goToFolder(folder) {
  if (folder === null) {
    openFolders.value = [];
  }
  const selectedFolderIndex = openFolders.value.findIndex(file => file?.id === folder.id);
  openFolders.value = openFolders.value.slice(0, selectedFolderIndex + 1);
}

function upload(file) {
  
  const parentFolder = openFolders.value.length > 0 ? openFolders.value.at(-1) : null;
  const loading = ElLoading.service({
    lock: true,
    text: 'Loading',
    background: 'rgba(0, 0, 0, 0.7)',
  });

  uploadFile(parentFolder, {
    name: file?.name ?? null,
    file: file.raw,
  }).catch(error => {
    const message = error?.response?.data?.message ?? 'Возникла ошибка в ходе выполнения запроса';
    ElMessageBox.confirm(message, 'Ошибка', {
      confirmButtonText: 'Ок',
      type: 'error',
      center: true,
      showCancelButton: false,
      showClose: false
    });
  }).finally(() => {
    loading.close();
  });
}

function showFileInfo(file) {
  fileInfo.visible = true;
  fileInfo.file = file;
}

onMounted(() => {
  const loading = ElLoading.service({
    lock: true,
    text: 'Loading',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  getFiles().finally(() => {
    loading.close();
  });
});
</script>

<template>
  <div class="bg-stone-200 w-screen h-screen">
    <el-container>
      <el-header class="flex flex-row justify-between mx-2">
        <div class="flex flex-row items-center">
          <el-image :src="logoUrl" class="w-20"/>
          <p class="text-3xl">Storage</p>
        </div>

        <MainFileInfoBlock
          :visible="fileInfo.visible"
          :file="fileInfo.file"
          @hide="fileInfo.visible = false"
        />

        <div class="flex items-center text-2xl">
          <div class="hover:bg-stone-400 hover:cursor-pointer rounded-3xl px-2 pt-1">
            <el-popover :width="400" trigger="click">
              <template #reference>
                <el-icon id="setting">
                  <Setting/>
                </el-icon>
              </template>
              <div>
                <p class="text-base font-semibold">Настройки</p>
              </div>
            </el-popover>
          </div>
          <div class="hover:bg-stone-400 hover:cursor-pointer rounded-3xl px-2 pt-1">

            <el-popconfirm
              title="Хотите выйти?"
              confirmButtonText="Да"
              cancelButtonText="Нет"
              @confirm="exit"
            >
              <template #reference>
                <el-icon><House/></el-icon>
              </template>
            </el-popconfirm>
          </div>
        </div>
      </el-header>

      <el-container class="bg-white rounded-2xl mx-5 mt-3">
        <el-aside width="200px" class="bg-stone-100 rounded-l-2xl p-3 pt-5">
          <div class="mb-3">
            <el-upload
              ref="uploadRef"
              name="file"
              :auto-upload="false"
              :show-file-list="false"
              :on-change="upload"
            >
              <el-button size="large" type="warning" class="w-full" style="font-size: 18px;" round>
                <el-icon class="mr-3">
                  <Upload/>
                </el-icon>
                Загрузить
              </el-button>
            </el-upload>
          </div>
          <div>
            <el-button
              size="large"
              class="w-full"
              style="font-size: 18px;"
              @click="Dialog.show()"
              round
            >
              <el-icon class="mr-3">
                <Plus/>
              </el-icon>
              Создать папку
            </el-button>
          </div>
        </el-aside>
        <el-container>
          <el-main>
            <MainWorkingPlace
              :files="selectedFiles"
              :loading="files.loading"
              :open-folders="openFolders"
              @open="open"
              @create-folder="Dialog.show()"
              @show-file-info="showFileInfo"
              @upload-file="upload"
              @go-to-folder="goToFolder"
            />
          </el-main>
          <el-footer>Footer</el-footer>
        </el-container>
      </el-container>
    </el-container>
  </div>

  <el-dialog
    v-model="Dialog.visible"
    title="Введите название для новой папки"
    width="30%"
    align-center
  >
    <el-input v-model="folderName" placeholder=""/>
    <template #footer>
      <span class="dialog-footer">
        <el-button
          type="primary"
          :loading="Dialog.process"
          @click="createFolder"
        >
          Создать
        </el-button>
      </span>
    </template>
  </el-dialog>
</template>

<style scoped>
#setting:active {
  transform: rotate(90deg);
  -webkit-transform: rotate(90deg);
  -ms-transform: rotate(90deg);
}
:deep(.el-aside .el-upload.el-upload--text) {
  width: 100%;
}
</style>