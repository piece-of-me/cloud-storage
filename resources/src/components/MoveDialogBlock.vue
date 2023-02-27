<script>
export default {
  name: 'MoveDialog',
};
</script>
<script setup>
import DirectoryTree from '@/components/DirectoryTree.vue';
import { ref } from "vue";
defineEmits(['hide', 'startMoving']);
defineProps({
  visibility: {
    type: Boolean,
    required: true,
  },
  fileId: {
    type: Number,
    required: true,
  },
  inProcess: {
    type: Boolean,
    required: true,
  }
});
const selectedFolderId = ref(undefined);
function selectFolder(id) {
  selectedFolderId.value = id;
}
</script>

<template>
  <el-dialog
    v-model="$props.visibility"
    title="Куда переместить?"
    width="30%"
    @close="$emit('hide')"
    align-center
    destroy-on-close
  >
    <DirectoryTree
      :fileId="fileId"
      @select-folder="selectFolder"
    />

    <template #footer>
      <el-button @click="$emit('hide')">Отмена</el-button>
      <el-button
        type="primary"
        :disabled="selectedFolderId === undefined"
        :loading="inProcess"
        @click="$emit('startMoving', selectedFolderId)"
      >
        Переместить
      </el-button>
    </template>
  </el-dialog>
</template>

<style scoped>

</style>