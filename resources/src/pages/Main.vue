<script setup>
import logoUrl from 'images/logo.png';
import MainBlockHeader from '@/components/MainBlockHeader.vue';
import MainWorkingPlace from '@/components/MainWorkingPlace.vue';
import { useFileStore } from '@/store/file.store.js';
import { useUserStore } from "@/store/user.store";
import { reactive, computed, ref, onMounted } from 'vue';
import { ElLoading } from 'element-plus';
import { useRouter } from 'vue-router';

const { files, getFiles, uploadFile } = useFileStore();
const { logout } = useUserStore();
const $router = useRouter();

const openFolders = ref([]);
const selectedFiles = computed(() => {
  const selectedFolder = openFolders.value.length <= 0 ? null : openFolders.value[openFolders.value.length - 1];
  return files.data
    .filter(file => file?.parentId === selectedFolder?.id || selectedFolder === null && file?.parentId === null)
    .map(file => {
      file.name = file.name.length <= 25 ? file.name : file.name.slice(0, 12).trim() + ' . . . ' + file.name.slice(-12).trim();
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
  uploadFile(parentFolder, {
    name: folderName.value,
    type: 2
  }).then(data => {
    console.debug(data);
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

function upload(file, ref = uploadRef) {
  const parentFolder = openFolders.value.length > 0 ? openFolders.value.at(-1) : null;
  const loading = ElLoading.service({
    lock: true,
    text: 'Loading',
    background: 'rgba(0, 0, 0, 0.7)',
  });

  uploadFile(parentFolder, {
    name: file?.name ?? null,
    file: file.raw,
  }).then(data => {
    ref.value.abort();
    ref.value.clearFiles();
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

        <div :class="['bg-stone-600 h-full rounded-b-lg w-3/4 px-5 flex items-center', {hidden: !fileInfo.visible}]">
          <div class="mr-3 text-white font-bold w-1/6">
            <el-popover
              placement="bottom"
              :width="200"
              trigger="hover"
            >
              <template #reference>
                <el-icon><QuestionFilled /></el-icon>
              </template>
              <p><strong>Размер:</strong> {{ fileInfo.file.size }}</p>
              <p><strong>Модификации:</strong> {{ fileInfo.file.updatedAt }}</p>
            </el-popover>
            {{ fileInfo.file.name }}
          </div>
          <div class="flex flex-row justify-between w-full">
            <div>
              <el-button type="info"><el-icon class="mr-2"><Share /></el-icon> Поделиться</el-button>
              <el-button type="info"><el-icon class="mr-2"><Download /></el-icon> Скачать</el-button>
            </div>

            <div>
              <el-button><el-icon class="mr-2"><Edit /></el-icon> Переименовать</el-button>
              <el-button><el-icon class="mr-2"><Folder /></el-icon> Переместить</el-button>
              <el-button><el-icon class="mr-2"><DeleteFilled /></el-icon> Удалить</el-button>
              <el-button><el-icon class="mr-2"><CopyDocument /></el-icon> Копировать</el-button>
              <el-button icon="CloseBold" circle @click="fileInfo.visible = false;"/>
            </div>
          </div>
        </div>

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
            <MainBlockHeader
              :openFolders="openFolders"
              @go-to-folder="goToFolder"
              class="mb-3"
            />

            <MainWorkingPlace
              :files="selectedFiles"
              :loading="files.loading"
              @open="open"
              @create-folder="Dialog.show()"
              @show-file-info="showFileInfo"
              @upload-file="upload"
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