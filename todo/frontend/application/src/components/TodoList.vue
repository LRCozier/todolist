<template>
  <div class="todo-list">
    <TodoItem
      v-for="task in tasks"
      :key="task.id"
      :task="task"
      @toggle="toggleTask"
      @edit="editTask"
      @delete="deleteTask"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Task } from '../types/interfaces';
import TodoItem from './TodoItem.vue';

export default defineComponent({
  name: 'TodoList',
  components: { TodoItem },
  props: {
    tasks: {
      type: Array as PropType<Task[]>,
      required: true
    }
  },
  emits: ['toggle', 'edit', 'delete'],
  methods: {
    toggleTask(id: number) {
      this.$emit('toggle', id);
    },
    editTask(task: Task) {
      this.$emit('edit', task);
    },
    deleteTask(id: number) {
      this.$emit('delete', id);
    }
  }
});
</script>