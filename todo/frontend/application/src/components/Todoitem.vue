<template>
  <div class="todo-item" :class="{ completed: task.completed}">
    <input type="checkbox" :checked="task.completed" @change="$emit('toggle', task.id)" />
    <span>{{ task.title }}</span>
    <button @click="$emit('edit', task)"></button>
    <button @click="$emit('delete', task.id)"></button>
  </div>
</template>

<script lang="ts">
import { defineComponent, DefineComponent, PropType } from 'vue';
import {Task} from '@/types/task';

export default defineComponent(
  {
    name: 'TodoItem',
    props: {
      task: {
        type: Object as PropType<Task>,
          required: true
      }
    },
    emits: ['toggle', 'edit', 'delete']
  }
);
</script>

<style>

</style>