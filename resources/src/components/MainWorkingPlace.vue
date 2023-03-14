<script>
export default {
  name: 'MainWorkingPlace',
};
</script>

<script setup>
import ContextMenu from '@/components/ContextMenu.vue';
import MainBlockHeader from '@/components/MainBlockHeader.vue';
import { getFileLogoUrl } from '@/utils/extensions.js';
import { reactive, computed, ref } from 'vue';

const $emit = defineEmits(['open', 'createFolder', 'uploadFile', 'showFileInfo', 'goToFolder']);
const $props = defineProps({
  files: {
    type: [Array, null],
    required: true,
  },
  loading: {
    type: Boolean,
    required: true,
  },
  openFolders: {
    type: Array,
    required: true,
  },
});

let selectedSortingOption = ref('');
let selectedSortingType = ref('');
let grouping = ref(true);

let uploadRef = ref(null);
const contextMenu = reactive({
  visibility: false,
  top: 0,
  left: 0,
  show(event) {
    this.top = event.pageY || event.clientY;
    this.left = event.pageX || event.clientX;
    this.visibility = true;
  },
  hide() {
    this.visibility = false;
  },
});

const clickHandler = {
  type: '',
  leftClick(file) {
    this.type = 'leftClick';
    setTimeout(() => {
      if (this.type !== 'leftClick') return;
      $emit('showFileInfo', file);
      this.type = '';
    }, 300);
  },
  dbClick(file) {
    this.type = 'dbClick';
    $emit('open', file);
    this.type = '';
  },
};
const sortedFiles = computed(() => {
  if (grouping.value) {
    const folders = $props.files.filter(file => file.typeId === 2);
    const files = $props.files.filter(file => file.typeId !== 2);
    if (selectedSortingOption.value.length && selectedSortingType.value.length) {
      return [
        ...folders.sort(getSortingMethod(selectedSortingType.value, selectedSortingOption.value)),
        ...files.sort(getSortingMethod(selectedSortingType.value, selectedSortingOption.value)),
      ];
    } else {
      return [...folders, ...files];
    }
  } else {
    return selectedSortingOption.value.length && selectedSortingType.value.length ?
      $props.files.sort(getSortingMethod(selectedSortingType.value, selectedSortingOption.value))
      : $props.files;
  }
});

function onUploadFile(file) {
  $emit('uploadFile', file)
}
function goToFolder(folder) {
  $emit('goToFolder', folder);
}

function getSortingMethod(type, method) {
  const sortingMethods = {
    getSortedByName(method) {
      return method === 'increase' ?
        (a, b) => a.name.localeCompare(b.name)
        : (a, b) => b.name.localeCompare(a.name);
    },
    getSortedBySize(method) {
      return method === 'increase' ?
        (a, b) => a.size - b.size
        : (a, b) => b.size - a.size;
    },
    getSortedByType(method) {
      return method === 'increase' ?
        (a, b) => a.extension === null ? 0 : a.extension.localeCompare(b.extension)
        : (a, b) => b.extension === null ? 0 : b.extension.localeCompare(a.extension);
    },
    getSortedByDate(method) {
      return method === 'increase' ?
        (a, b) => (new Date(a.updatedAtJSFormat)) - (new Date(b.updatedAtJSFormat))
        : (a, b) => (new Date(b.updatedAtJSFormat)) - (new Date(a.updatedAtJSFormat));
    },
  };
  switch (method) {
    case 'name': return sortingMethods.getSortedByName(type);
    case 'size': return sortingMethods.getSortedBySize(type);
    case 'type': return sortingMethods.getSortedByType(type);
    case 'date': return sortingMethods.getSortedByDate(type);
    default: return null;
  }
}
</script>

<template>
  <MainBlockHeader
    :open-folders="openFolders"
    v-model:selected-sorting-option="selectedSortingOption"
    v-model:selected-sorting-type="selectedSortingType"
    v-model:grouping="grouping"
    @go-to-folder="goToFolder"
    class="mb-3"
  />
  <div
    class="flex flex-row flex-wrap"
    @click.self="contextMenu.hide()"
    v-loading="!loading"
  >
    <div
      v-if="loading && files?.length"
      class="flex flex-wrap"
    >
      <div class="w-32 h-max hover:bg-stone-100 hover:cursor-pointer rounded-2xl pb-3 mr-3"
           v-for="file in sortedFiles"
           @click.left="clickHandler.leftClick(file)"
           @dblclick="clickHandler.dbClick(file)"
           @contextmenu.prevent="contextMenu.show"
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
      v-else-if="loading && files && files.length <= 0"
      class="flex justify-center w-full"
    >
      <el-empty>
        <div class="flex" v-if="files?.length">
          <el-button type="primary mr-3" @click="$emit('createFolder')">Создать папку</el-button>
          <el-upload
            ref="uploadRef"
            name="file"
            :auto-upload="false"
            :show-file-list="false"
            :on-change="onUploadFile"
          >
            <el-button type="primary">Загрузить</el-button>
          </el-upload>
        </div>
      </el-empty>
    </div>
  </div>
  <ContextMenu
    :show="contextMenu.visibility"
    :top="contextMenu.top"
    :left="contextMenu.left"
  />
</template>

<style scoped>

</style>