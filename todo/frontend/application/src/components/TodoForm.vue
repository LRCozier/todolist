<template>
  <form
  @submit.prevent="handleSubmit"
  class="todo-form">
  <input
  type="text"
  v-model="title"
  placeholder="Title"
  required />
  <textarea 
  v-model="description"
  placeholder="Enter Task Here">
  </textarea>
  <div class="form-actions">
    <button type="submit">
      Submit
    </button>
    <button
    v-if="showCancel"
    type="button"
    @click="handleCancel">
    Cancel
    </button>
  </div>
  </form>
</template>

<script lang="ts">
import { defineComponent, PropType, ref, watch } from 'vue';
import { Task } from '../types/interfaces';

export default defineComponent({
  name: 'TodoForm',
  props: {
    initialTask:{
      type: Object as PropType<Partial<Task>>,
      default: null,
    },
    submitText: {
      type: String,
      default: 'Add Task',
    },
    showCancel: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['submit', 'cancel', 'create'],
  setup(props, {emit}) {
    const title = ref('');
    const description = ref('');

    //populate form if editting an existing task
    watch( () => props.initialTask, (task) => {
      if (task) {
        title.value = task.title || '';
        description.value = task.description || '';
      }
    }, {immediate: true});

    const handleSubmit = () => {
      const taskData = {
        title: title.value,
        description: description.value,
      };

    if (props.initialTask?.id) {
      emit('submit', taskData)
    } else {
      emit('create', taskData.title);
    }

    // reset form if not editting a task
    if (!props.initialTask) {
      title.value = '';
      description.value = '';
    }
    };

    const handleCancel = () => {
      emit('cancel');
    }

    return { title, description, handleSubmit, handleCancel};

  },
});
</script>

<style>

</style>