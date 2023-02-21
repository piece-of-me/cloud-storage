<script>
export default {
  name: 'MainWorkingPlace'
}
</script>

<script setup>
import folderImgUrl from 'images/folder-icon.png';
import ContextMenu from '@/components/ContextMenu.vue';
import { getExtensionImage } from '@/utils/extensions.js';
import { reactive, ref } from 'vue';

const $emit = defineEmits(['open', 'createFolder', 'uploadFile', 'showFileInfo']);
const $props = defineProps({
  files: {
    type: Array,
    required: true,
  },
  loading: {
    type: Boolean,
    required: true,
  },
});

let uploadRef = ref(null);

const contextMenu = reactive({
  visibility: false,
  top: 0,
  left: 0,
  show(event) {
    this.top = event.pageY || event.clientY;
    this.left = event.pageX || event.clientX;
    this.visibility = true
  },
  hide() {
    this.visibility = false
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

function onUploadFile(file) {
  $emit('uploadFile', file, uploadRef)
}
function getFileLogoUrl(file) {
  switch (+file.typeId) {
    case 1:
      return getExtensionImage(file.extension);
    case 2:
      return folderImgUrl;
    case 3:
      return file.path;
  }
}
</script>

<template>
  <div
    class="flex flex-row flex-wrap"
    @click.self="contextMenu.hide()"
    v-loading="!$props.loading"
  >
    <div
      v-if="$props.loading && $props.files.length"
      class="flex"
    >
      <div class="w-32 h-max hover:bg-stone-100 hover:cursor-pointer rounded-2xl pb-3 mr-3"
           v-for="file in $props.files"
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
        <p class="text-center tracking-narrowly leading-4">{{ file.name }}</p>
      </div>
    </div>
    <div
      v-else-if="$props.loading && $props.files.length <= 0"
      class="flex justify-center w-full"
    >
      <el-empty>
        <div class="flex">
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