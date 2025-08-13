<template>
  <div class="todo-item" :class="{ completed: task.completed }">
    <input type="checkbox" :checked="task.completed" @change="$emit('toggle', task.id)" />
    <span>{{ task.title }}</span>
    <button @click="$emit('edit', task)"><PencilSquareIcon class="h-5 w-5" /></button>
    <button @click="$emit('delete', task.id)"><TrashIcon class="h-5 w-5 text-red-500" /></button>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Task } from '../types/interfaces';
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline';

export default defineComponent({
  name: 'TodoItem',
  props: {
    task: {
      type: Object as PropType<Task>,
      required: true
    }
  },
  emits: ['toggle', 'edit', 'delete']
});
</script>

<style>

</style>