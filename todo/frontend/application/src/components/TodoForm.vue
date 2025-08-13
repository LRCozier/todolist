<template>
  <form @submit.prevent="handleSubmit" class="todo-form">
    <input type="text" v-model="title" placeholder="Title" required />
    <textarea v-model="description" placeholder="Enter Task Here" required autofocus></textarea>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">
        {{ initialTask ? 'Update Task' : 'Add Task' }}
      </button>
      <button v-if="initialTask" type="button" @click="handleCancel">Cancel</button>
    </div>
  </form>
</template>

<script lang="ts">
import { defineComponent, PropType, ref, watch } from 'vue';
import { Task, TaskCreatePayload, TaskUpdatePayload } from '../types/interfaces';

export default defineComponent({
  name: 'TodoForm',
  props: {
    initialTask: {
      type: Object as PropType<Task | null>,
      default: null,
    },
  },
  emits: ['submit-task', 'cancel'],
  setup(props, { emit }) {
    const title = ref('');
    const description = ref('');

    // Watch for changes in initialTask
    watch(() => props.initialTask, (task) => {
      if (task) {
        title.value = task.title || '';
        description.value = task.description || '';
      } else {
        // Reset the form
        title.value = '';
        description.value = '';
      }
    }, { immediate: true });

    const handleSubmit = () => {
      const taskData: TaskCreatePayload | TaskUpdatePayload = {
        title: title.value,
        description: description.value,
      };
      
      emit('submit-task', taskData);
    };

    const handleCancel = () => {
      emit('cancel');
    };

    return { title, description, handleSubmit, handleCancel };
  },
});
</script>