<script>
export default {
  name: 'ContextMenu'
}
</script>

<script setup>
import { computed, reactive, ref } from 'vue';

const $props = defineProps({
  show: {
    type: Boolean,
    required: true,
  },
  top: {
    type: Number,
    required: true,
  },
  left: {
    type: Number,
    required: true,
  }
});

const style = computed(() => {
  return {
    top: $props.top + 'px',
    left: $props.left + 'px',
  };
});

function close() {

}
</script>

<template>
  <div id="context-menu" class="p-1.5 rounded-lg drop-shadow-lg" v-show="$props.show" :style="style" ref="context" tabindex="0" @blur="close">
    <ul>
      <li> List item 1 </li>
      <li> List item 2 </li>
    </ul>
  </div>
</template>

<style scoped>
#context-menu {
  position: fixed;
  background: white;
  z-index: 999;
  outline: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
  cursor: pointer;
}
</style>