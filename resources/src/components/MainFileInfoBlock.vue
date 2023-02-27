<script>
export default {
  name: 'FileInfoBlock'
};
</script>

<script setup>
import MoveDialogBlock from '@/components/MoveDialogBlock.vue';
import { ref, computed, reactive } from 'vue';
import { ElLoading, ElMessageBox } from 'element-plus';
import { useFileStore } from '@/store/file.store';
const { getFilesByParentId, renameFile, moveFile, deleteFile } = useFileStore();

const $emit = defineEmits(['hide']);
const $props = defineProps({
  file: {
    type: Object,
    default: false,
  },
  visible: {
    type: Boolean,
    required: true,
  }
});
let newName = ref('');

const RenameDialog = reactive({
  visible: false,
  process: false,
  show() {
    newName.value = $props.file?.name;
    this.visible = true;
  },
  hide() {
    this.visible = false;
  },
});

function rename() {
  RenameDialog.process = true;
  renameFile($props.file.id, newName.value).finally(() => {
    RenameDialog.hide();
    RenameDialog.process = false;
    $emit('hide');
  });
}

function remove() {
  const loading = ElLoading.service({
    lock: true,
    text: 'Удаление',
    background: 'rgba(0, 0, 0, 0.7)',
  });
  deleteFile($props.file.id).finally(() => {
    loading.close();
    $emit('hide');
  });
}

function hide() {
  newName.value = '';
  $emit('hide');
}

const deleteTitle = computed(() => {
  return 'Удалить ' + ($props.file.typeId === 2 ? 'папку' : 'файл');
});

const MoveDialog = reactive({
  visible: false,
  process: false,
  show() {
    this.visible = true;
  },
  hide() {
    this.visible = false;
  },
});

function startMoving(id) {
  MoveDialog.process = true;
  moveFile($props.file.id, id).then(() => {
    MoveDialog.hide();
    $emit('hide');
  }).catch(error => {
    const message = error.response?.data?.message ?? 'Файл уже существует';
    ElMessageBox.alert(message, 'Ошибка при перемещении', {
      showConfirmButton: false,
      showClose: false,
      closeOnClickModal: true,
      closeOnPressEscape: true,
      center: true,
    });
  }).finally(() => {
    MoveDialog.process = false;
  });
}

</script>

<template>
  <div :class="['bg-stone-600 h-full rounded-b-lg w-3/4 px-5 flex items-center', {hidden: !visible}]">
    <div class="mr-3 text-white font-bold w-1/6">
      <el-popover
        placement="bottom"
        :width="200"
        trigger="hover"
      >
        <template #reference>
          <el-icon><QuestionFilled /></el-icon>
        </template>
        <p><strong>Размер:</strong> {{ file.size }}</p>
        <p><strong>Модификации:</strong> {{ file.updatedAt }}</p>
      </el-popover>
      {{ file.shortName }}
    </div>
    <div class="flex flex-row justify-between w-full">
      <div>
        <el-button type="info"><el-icon class="mr-2"><Share /></el-icon> Поделиться</el-button>
        <el-button type="info"><el-icon class="mr-2"><Download /></el-icon> Скачать</el-button>
      </div>

      <div>
        <el-button @click="RenameDialog.show()">
          <el-icon class="mr-2"><Edit /></el-icon> Переименовать
        </el-button>
        <el-button @click="MoveDialog.show()">
          <el-icon class="mr-2"><Folder /></el-icon> Переместить
        </el-button>
        <el-popconfirm
          :title="deleteTitle"
          confirm-button-text="Да"
          cancel-button-text="Нет"
          @confirm="remove"
        >
          <template #reference>
            <el-button>
              <el-icon class="mr-2"><DeleteFilled /></el-icon> Удалить
            </el-button>
          </template>
        </el-popconfirm>
        <el-button><el-icon class="mr-2"><CopyDocument /></el-icon> Копировать</el-button>
        <el-button icon="CloseBold" circle @click="hide"/>
      </div>
    </div>

    <el-dialog
      v-model="RenameDialog.visible"
      title="Введите новое название"
      width="30%"
      align-center
    >
      <el-input v-model="newName"/>
      <template #footer>
      <span class="dialog-footer">
        <el-button
          type="primary"
          :loading="RenameDialog.process"
          @click="rename"
        >
          Переименовать
        </el-button>
      </span>
      </template>
    </el-dialog>

    <MoveDialogBlock
      :visibility="MoveDialog.visible"
      :fileId="file.id"
      :inProcess="MoveDialog.process"
      @hide="MoveDialog.hide()"
      @startMoving="startMoving"

    />
  </div>
</template>

<style scoped>

</style>