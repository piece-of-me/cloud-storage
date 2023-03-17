<script>
export default {
  name: 'MainBlockHeader',
};
</script>

<script setup>
import { computed, onMounted } from 'vue';
const $emit = defineEmits(['goToFolder', 'update:selectedSortingOption', 'update:selectedSortingType', 'update:grouping']);
const $props = defineProps({
  openFolders: {
    type: Array,
    required: true,
  },
  selectedSortingOption: {
    type: String,
    required: true,
  },
  selectedSortingType: {
    type: String,
    required: true,
  },
  grouping: {
    type: Boolean,
    required: true,
  },
});

const sortingOptions = {
  name: 'По названию',
  size: 'По размеру',
  type: 'По типу',
  date: 'По дате',
};
const sortingType = {
  increase: 'По возрастанию',
  decrease: 'По убыванию',
};

const selectedSortingOption = computed({
  set(value) {
    localStorage.setItem('sorting-option', value);
    $emit('update:selectedSortingOption', value);
  },
  get() {return $props.selectedSortingOption;},
});
const selectedSortingType = computed({
  set(value) {
    localStorage.setItem('sorting-type', value);
    $emit('update:selectedSortingType', value);
  },
  get() {return $props.selectedSortingType;},
});
const grouping = computed({
  set(value) {
    localStorage.setItem('grouping', value);
    $emit('update:grouping', value);
  },
  get() {return $props.grouping;},
});

onMounted(() => {
  $emit('update:selectedSortingOption', localStorage.getItem('sorting-option') ?? 'name');
  $emit('update:selectedSortingType', localStorage.getItem('sorting-type') ?? 'increase');
  $emit('update:grouping', localStorage.getItem('grouping') ?? true);
});
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
        <el-icon class="mr-3" v-show="selectedSortingType === 'increase'">
          <SortUp/>
        </el-icon>
        <el-icon class="mr-3" v-show="selectedSortingType === 'decrease'">
          <SortDown/>
        </el-icon>
        {{ sortingOptions[selectedSortingOption] }}
      </el-button>
      <template #dropdown>
        <el-dropdown-menu>
          <el-dropdown-item
            v-for="(option, key) in sortingOptions" @click="selectedSortingOption = key">
            <el-icon v-if="selectedSortingOption === key"><Select/></el-icon>
            {{ option }}
          </el-dropdown-item>
          <el-dropdown-item
            v-for="(type, key) in sortingType"
            @click="selectedSortingType = key"
            :divided="'increase' === key">
            <el-icon v-if="selectedSortingType === key"
            >
              <Select/></el-icon> {{ type }}
          </el-dropdown-item>
          <el-dropdown-item divided>
            <el-checkbox v-model="grouping" label="Группировать" size="small"/>
          </el-dropdown-item>
        </el-dropdown-menu>
      </template>
    </el-dropdown>
  </div>
</template>

<style scoped>

</style>