<template>
  <div class="todo-list">
    <Todoitem
    v-for="task in tasks"
    :key="'task.id'"
    :task="task"
    @toggle="toggleTask"
    @edit="editTask"
    @delete="deleteTask" />
  </div>
</template>

<script lang="ts">
import { defineComponent, DefineComponent, PropType } from 'vue';
import {Task} from '@/types/task';
import Todoitem from './Todoitem.vue';

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