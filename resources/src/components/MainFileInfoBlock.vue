<script>
export default {
  name: 'MainFileInfoBlock'
};
</script>

<script setup>
import DirectoryTreeDialog from '@/components/DirectoryTreeDialog.vue';
import { ref, computed, reactive } from 'vue';
import { ElLoading, ElMessageBox } from 'element-plus';
import { useFileStore } from '@/store/file.store';
const { renameFile, moveFile, copyFile, deleteFile } = useFileStore();

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

const DirectoryTreeDialogModal = reactive({
  visible: false,
  process: false,
  type: null,
  /** @values [1 - 'copy'(Копирование), 2 - 'move'(Перемещение)] */
  show(type) {
    this.type = type;
    this.visible = true;
  },
  hide() {
    this.type = null;
    this.visible = false;
  },
});

function confirm(id) {
  DirectoryTreeDialogModal.process = true;
  const method = DirectoryTreeDialogModal.type === 1 ? copyFile : moveFile;
  method($props.file.id, id).then(() => {
    DirectoryTreeDialogModal.hide();
    $emit('hide');
  }).catch(error => {
    const message = error.response?.data?.message ?? 'Файл уже существует';
    const title = DirectoryTreeDialogModal.type === 1 ? 'Ошибка при копировании' : 'Ошибка при перемещении';
    ElMessageBox.alert(message, title, {
      showConfirmButton: false,
      showClose: false,
      closeOnClickModal: true,
      closeOnPressEscape: true,
      center: true,
    });
  }).finally(() => {
    DirectoryTreeDialogModal.process = false;
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
        <el-button @click="DirectoryTreeDialogModal.show(2)">
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
        <el-button @click="DirectoryTreeDialogModal.show(1)">
          <el-icon class="mr-2"><CopyDocument /></el-icon> Копировать
        </el-button>
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

    <DirectoryTreeDialog
      :visibility="DirectoryTreeDialogModal.visible"
      :fileId="file.id"
      :inProcess="DirectoryTreeDialogModal.process"
      :type="DirectoryTreeDialogModal.type"
      @hide="DirectoryTreeDialogModal.hide()"
      @confirm="confirm"
    />
  </div>
</template>

<style scoped>

</style>