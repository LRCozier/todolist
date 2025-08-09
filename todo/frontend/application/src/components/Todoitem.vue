<template>
  <div class="todo-item" :class="{ completed: task.completed}">
    <input type="checkbox" :checked="task.completed" @change="$emit('toggle', task.id)" />
    <span>{{ task.title }}</span>
    <button @click="$emit('edit', task)"><font-awesome-icon :icon="['fal', 'pencil']" />></button>
    <button @click="$emit('delete', task.id)"><font-awesome-icon :icon="['fal', 'trash']" /></button>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Task } from '../types/tasks';



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