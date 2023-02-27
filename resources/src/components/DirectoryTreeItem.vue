<script>
export default {
  name: 'DirectoryTreeItem'
};
</script>
<script setup>
import folderImgUrl from 'images/folder-icon.png';
const $emit = defineEmits(['showSubFolders', 'hideSubFolders', 'selectFolder']);
const $props = defineProps({
  openFoldersId: {
    type: Array,
    required: true,
  },
  selectedFolderId: {
    type: Number,
    required: true,
  },
  folder: {
    type: Object,
    required: true,
  }
});

function toggleCaret(id) {
  if ($props.openFoldersId.includes(id)) {
    $emit('hideSubFolders', id);
  } else {
    $emit('showSubFolders', id);
  }
}
</script>

<template>
  <div>
  <div :class="['h-12 align-middle flex flex-row hover:bg-stone-200', {'bg-stone-200': selectedFolderId === folder.id}]">
    <div class="pt-3" v-if="$slots.default().length">
      <el-icon
        class="hover:cursor-pointer"
        v-show="!openFoldersId.includes(folder.id)"
        @click="toggleCaret(folder.id)"
      >
        <CaretRight/>
      </el-icon>
      <el-icon
        class="hover:cursor-pointer"
        v-show="openFoldersId.includes(folder.id)"
        @click="toggleCaret(folder.id)"
      >
        <CaretBottom/>
      </el-icon>
    </div>

    <el-image
      @click="$emit('selectFolder', folder.id)"
      :src="folderImgUrl"
      :class="['h-12 hover:cursor-pointer', {'ml-[14px]' : !$slots.default().length}]"
    />
    <div class="h-12 w-full p-3">{{ folder.name }}</div>
  </div>
    <div class="ml-5" v-show="openFoldersId.includes(folder.id)">
      <slot></slot>
    </div>
  </div>
</template>

<style scoped>

</style>