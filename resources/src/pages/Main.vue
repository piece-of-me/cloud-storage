<script setup>
import logoUrl from 'images/logo.png';
import folderImgUrl from 'images/folder-icon.png';
import MainBlockHeader from '@/components/MainBlockHeader.vue';
import { useFileStore } from '@/store/file.store.js';
import { useUserStore } from "@/store/user.store";
import { reactive, computed, ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const { getFiles, uploadFile } = useFileStore();
const { logout } = useUserStore();
const $router = useRouter();

const folders = ref([]);

let parentFolder = ref(null);
let folderName = ref('');

const correctFolders = computed(() => {
  return folders.value.map(folder => {
    folder.name = folder.name.length <= 25 ? folder.name : folder.name.slice(0, 12).trim() + ' . . . ' + folder.name.slice(-12).trim();
    return folder;
  });
});

const folderHeaderMenu = reactive({
  name: '',
  visible: false,
});

const Dialog = reactive({
  visible: false,
  show() {
    this.visible = true;
  },
  hide() {
    this.visible = false;
  },
});

function onLeftClickOnFolder(folder) {
  folderHeaderMenu.name = folder.name;
  folderHeaderMenu.visible = true;
}

function onRightClickOnFolder(event) {
  console.debug('right click');
  console.debug(event);
}

function onLeftClickOnTable() {
  folderHeaderMenu.visible = false;
}

function createFolder() {
  Dialog.hide();
  uploadFile(parentFolder, {
    name: folderName.value,
    type: 2
  }).then(data => {
    console.debug(data);
  });
  folderName.value = '';
}

function exit() {
  logout().then(() => {
    $router.replace({name: 'login'});
  });
}

onMounted(() => {
  getFiles().then(response => {
    if (response.status === 200 && response.hasOwnProperty('data')) {
      folders.value = response.data;
    }
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

        <div :class="['bg-stone-600 h-full rounded-b-lg w-3/4 px-5 flex items-center', {hidden: !folderHeaderMenu.visible}]">
          <div class="mr-3 text-white font-bold w-1/6">
            <el-popover
              placement="bottom"
              :width="200"
              trigger="hover"
            >
              <template #reference>
                <el-icon><QuestionFilled /></el-icon>
              </template>
              asdasd
            </el-popover>
            {{ folderHeaderMenu.name }}
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
              <el-button icon="CloseBold" circle @click="folderHeaderMenu.visible = false;"/>
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
            <el-button size="large" type="warning" class="w-full" style="font-size: 18px;" round>
              <el-icon class="mr-3">
                <Upload/>
              </el-icon>
              Загрузить
            </el-button>
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
            <MainBlockHeader class="mb-3"/>

            <div class="flex flex-row flex-wrap" @click.self="onLeftClickOnTable">
              <div class="w-32 hover:bg-stone-100 hover:cursor-pointer rounded-2xl pb-3 mr-3"
                   v-for="folder in correctFolders"
                   @click="onLeftClickOnFolder(folder)"
                   @contextmenu.prevent="onRightClickOnFolder"
              >
                <div class="relative">
                  <el-image :src="folderImgUrl" class="w-full"/>
                  <el-tooltip
                    class="box-item"
                    effect="dark"
                    placement="right"
                    v-if="folder.public"
                  >
                    <template #content>
                      <p class="font-bold text-sm">
                        <el-icon>
                          <View/>
                        </el-icon>
                        {{ folder.views }}
                        <el-icon>
                          <Download/>
                        </el-icon>
                        {{ folder.downloads }}
                      </p>
                    </template>
                    <el-button type="info" icon="Link" circle class="absolute right-0 bottom-4"/>
                  </el-tooltip>

                </div>
                <p class="text-center tracking-narrowly leading-4">{{ folder.name }}</p>
              </div>
            </div>

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
</style>