<script>
export default {
  name: 'MainBlockHeader'
}
</script>

<script setup>
import { ref } from 'vue';
defineEmits(['goToFolder']);
const $props = defineProps({
  openFolders: {
    type: Array,
    required: true,
  }
});

let selectedSortingOption = ref('');
let selectedSortingType = ref(0);
const grouped = ref(true);
const sortingOptions = [
  'По названию',
  'По размеру',
  'По типу',
  'По дате',
];
const sortingType = [
  'По возрастанию',
  'По убыванию'
];
selectedSortingOption.value = sortingOptions[0];
</script>

<template>
  <div class="flex flex-row justify-between">
    <el-breadcrumb :separator-icon="ArrowRight">
      <el-breadcrumb-item class="text-xl cursor-pointer">
        <span class="font-semibold" @click="$emit('goToFolder', null)">Файлы</span>
      </el-breadcrumb-item>

      <el-breadcrumb-item
        v-for="folder in $props.openFolders"
        class="text-xl cursor-pointer"
      >
        <span class="font-semibold" @click="$emit('goToFolder', folder)">{{ folder.name }}</span>
      </el-breadcrumb-item>
    </el-breadcrumb>
    <el-dropdown trigger="click">
      <el-button type="primary">
        <el-icon class="mr-3" v-show="selectedSortingType === 0">
          <SortUp/>
        </el-icon>
        <el-icon class="mr-3" v-show="selectedSortingType === 1">
          <SortDown/>
        </el-icon>
        {{ selectedSortingOption }}
      </el-button>
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item
            v-for="option in sortingOptions" @click="selectedSortingOption = option">
            <el-icon v-if="selectedSortingOption === option"><Select/></el-icon>
            {{ option }}
          </el-dropdown-item>
          <el-dropdown-item
            v-for="(type, index) in sortingType"
            @click="selectedSortingType = index"
            :divided="index === 0">
            <el-icon v-if="selectedSortingType === index"><Select/></el-icon>
            {{ type }}
          </el-dropdown-item>
          <el-dropdown-item divided>
            <el-checkbox v-model="grouped" label="Группировать" size="small"/>
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<style scoped>

</style>