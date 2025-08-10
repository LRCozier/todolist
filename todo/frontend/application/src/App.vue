<template>
  <main class="app">
    <h1>Procrasti-not</h1>
    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <template v-else>
      <TodoForm @task-created="fetchTasks" />
      <TodoList :tasks="tasks" @toggle-task="toggleTask" @edit-task="editTask" @delete-task="deleteTask" />
    </template>
  </main>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import TodoForm from './components/TodoForm.vue';
import TodoList from './components/TodoList.vue';
import { TaskService } from './services/api';
import { Task } from './types/tasks';

const loading = ref(true);
const error = ref<string | null>(null);
const tasks = ref<Task[]>([]);

// Function to fetch tasks from the API
const fetchTasks = async () => {
  loading.value = true;
  try {
    tasks.value = await TaskService.getAll();
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to fetch tasks.';
  } finally {
    loading.value = false;
  }
};

// Placeholder functions for handling events
const toggleTask = (id: number) => {
  console.log(`Toggle task with ID: ${id}`);
};

const editTask = (id: number, newTitle: string) => {
  console.log(`Edit task with ID: ${id} to new title: ${newTitle}`);
};

const deleteTask = async (id: number) => {
  await TaskService.delete(id);
  fetchTasks();
};

onMounted(() => {
  fetchTasks();
});
</script>

<style scoped>

</style>