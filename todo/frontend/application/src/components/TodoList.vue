<template>
  <div class="todo-list">
    <TodoForm />
    <Todoitem
    v-for="task in task"
    :key="'task.id'"
    :task="task"
    @toggle="toggleTask"
    @edit="editTask"
    @delete="deleteTask" />
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import { Task } from '../types/interfaces';
import Todoitem from './Todoitem.vue';
import TodoForm from './TodoForm.vue';

export default defineComponent({
  name: 'TodoList',
  components: {Todoitem},
  props: {
    task: {
      type: Array as PropType<Task[]>,
      required: true
    }
  },
  emits: ['toggle', 'edit', 'delete'],
  methods: {
    toggleTask(id:number) {
      this.$emit('toggle', id);
    },
    editTask(task:Task) {
      this.$emit('edit', task);
    },
    deleteTask(id:number) {
      this.$emit('delete', id);
    }
  }
})
</script>

<style>

</style>