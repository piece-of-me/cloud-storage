<script>
export default {
  name: 'MainFileInfoBlock'
};
</script>

<script setup>
import DirectoryTreeDialog from '@/components/DirectoryTreeDialog.vue';
import { useClipboard } from "@vueuse/core";
import { ref, computed, reactive, watch } from 'vue';
import { ElLoading, ElMessage, ElMessageBox } from 'element-plus';
import { useFileStore } from '@/store/file.store';
const { copy: copyInClipboard } = useClipboard();
const { download, renameFile, moveFile, copyFile, shareFile, deleteFile } = useFileStore();

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
    $emit('hide');
  });
}

const shareSwitch = ref(false);
const ShareDialog = reactive({
  visible: false,
  process: false,
  title: computed(() => {
    let ending = '';
    switch ($props.file.typeId) {
      case 1: ending = ' файлом'; break;
      case 2: ending = ' папкой'; break;
      case 3: ending = ' изображением'; break;
    }
    return 'Поделиться' + ending;
  }),
  show() {
    this.visible = true;
  },
  hide() {
    this.visible = false;
  },
});
watch(() => $props.file.public, newValue => {
  shareSwitch.value = newValue;
});

function share() {
  ShareDialog.process = true;
  return shareFile($props.file.id).finally(() => {
    ShareDialog.process = false;
  });
}
function copy() {
  copyInClipboard($props.file.publicHash);
  ElMessage({
    message: 'Скопировано в буфер обмена',
    type: 'success',
    duration: 750,
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
        <p><strong>Размер:</strong> {{ file.sizeStr }}</p>
        <p><strong>Модификации:</strong> {{ file.updatedAt }}</p>
      </el-popover>
      {{ file.shortName }}
    </div>
    <div class="flex flex-row justify-between w-full">
      <div>
        <el-button type="info" @click="ShareDialog.show()"><el-icon class="mr-2"><Share /></el-icon> Поделиться</el-button>
        <el-button type="info" @click="downloadFile(file)"><el-icon class="mr-2"><Download /></el-icon> Скачать</el-button>
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

    <el-dialog
      v-model="ShareDialog.visible"
      :title="ShareDialog.title"
      width="30%"
      align-center
      destroy-on-close
    >
      <el-switch
        v-model="shareSwitch"
        class="mb-2"
        active-text="Открыть доступ по ссылке"
        :loading="ShareDialog.process"
        :before-change="share"
      />
      <div v-if="shareSwitch" class="mt-3">
        <div class="bg-neutral-200 p-3">
          <div class="flex flex-row items-center">
            <div>
              <el-icon class="mr-2" size="20px">
                <View/>
              </el-icon>
            </div>
            <div class="flex flex-col">
              <div class="text-lg">Просмотры:</div>
              <div class="lowercase">Количество переходов по ссылке:</div>
            </div>
            <div class="pl-3 text-2xl">
              {{ file.views }}
            </div>
          </div>
        </div>
        <el-button
          type="primary"
          class="mt-3"
          :loading="RenameDialog.process"
          @click="copy"
        >
          Копировать ссылку
          <el-icon class="ml-2">
            <CopyDocument/>
          </el-icon>
        </el-button>
      </div>
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