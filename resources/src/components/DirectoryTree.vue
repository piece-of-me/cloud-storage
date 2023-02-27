<script>
import { ref, h } from 'vue';
import DirectoryTreeItem from '@/components/DirectoryTreeItem.vue';
import { useFileStore } from '@/store/file.store';

export default {
  name: 'DirectoryTree',
  emits: ['selectFolder'],
  props: {
    fileId: {
      type: Object,
      required: true,
    }
  },
  setup($props, {emit: $emit}) {
    const openFoldersId = ref([]);
    const selectedFolderId = ref(undefined);
    const { getFoldersByParentId } = useFileStore();

    function showSubFolders(id) {
      openFoldersId.value.push(id);
    }

    function hideSubFolders(id) {
      openFoldersId.value = openFoldersId.value.filter(folderId => folderId !== id);
    }

    function selectFolder(id) {
      selectedFolderId.value = id;
      $emit('selectFolder', id);
    }

    function render(id) {
      return getFoldersByParentId(id)
        .filter(folder => folder.id !== $props.fileId)
        .map(folder => {
          return h(DirectoryTreeItem, {
            openFoldersId: openFoldersId.value,
            selectedFolderId: selectedFolderId.value,
            folder,
            onShowSubFolders: showSubFolders,
            onHideSubFolders: hideSubFolders,
            onSelectFolder: selectFolder,
          }, render(folder.id));
        });
    }

    return () => h(DirectoryTreeItem, {
      openFoldersId: openFoldersId.value,
      selectedFolderId: selectedFolderId.value,
      folder: {
        id: null,
        name: 'Главная',
      },
      onShowSubFolders: showSubFolders,
      onHideSubFolders: hideSubFolders,
      onSelectFolder: selectFolder,
    }, render(null));
  }
};
</script>
