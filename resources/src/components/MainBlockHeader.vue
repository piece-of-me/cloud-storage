<script>
export default {
  name: "MainBlockHeader"
}
</script>

<script setup>
import { ref } from 'vue';
const selectedSortingOption = ref('');
const selectedSortingType = ref(0);
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
    <el-breadcrumb separator="/">
      <el-breadcrumb-item class="text-2xl"><span class="font-semibold">Файлы</span></el-breadcrumb-item>
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
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<style scoped>

</style>