<template>
    <header>
      <h1>Procrasti-not</h1>
      <div v-if="user">
        <span>{{ user.email }}</span>
        <button @click="logout">Logout</button>
      </div>
    </header>
    <main class="app">
    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
      <TodoForm/>
      <TodoList v-else-if="user" />
  </main>
  <<footer>
    <p>&copy;{{ new Date().getFullYear() }} Luke Rudderham-Cozier</p>
  </footer>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import TodoForm from './components/TodoForm.vue';
import TodoList from './components/TodoList.vue';
import { TaskApi } from './services/api';
import { Task } from './types/interfaces';

const loading = ref(true);
const error = ref<string | null>(null);
const tasks = ref<Task[]>([]);

// Function to fetch tasks from the API
const fetchTasks = async () => {
  loading.value = true;
  try {
    tasks.value = await TaskApi.getAll();
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
  await TaskApi.delete(id);
  fetchTasks();
};

onMounted(() => {
  fetchTasks();
});
</script>

<style scoped>

</style>