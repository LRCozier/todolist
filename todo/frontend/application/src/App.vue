<template>
  <header>
    <h1>Procrasti-not</h1>
    <div v-if="isLoggedIn">
      <span>{{ user.email }}</span>
      <button @click="logout">Logout</button>
    </div>
  </header>
  <main class="app">
    <div v-if="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    
    <div v-else>
      <!-- Conditionally render the auth component or the main app content -->
      <Auth v-if="!isLoggedIn" @login-success="handleLoginSuccess" />

      <div v-else>
        <TodoForm
          :initial-task="editingTask"
          @submit-task="handleFormSubmit"
          @cancel="cancelEdit"
        />
        
        <TodoList
          :tasks="tasks"
          @toggle="toggleTask"
          @edit="editTask"
          @delete="deleteTask"
        />
      </div>
    </div>
  </main>
  <footer>
    <p>&copy;{{ new Date().getFullYear() }} Luke Rudderham-Cozier</p>
  </footer>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import TodoForm from './components/TodoForm.vue';
import TodoList from './components/TodoList.vue';
import Auth from './components/Auth.vue';
import { TaskApi } from './services/api';
import { Task, TaskCreatePayload, TaskUpdatePayload } from './types/interfaces';

const loading = ref(true);
const error = ref<string | null>(null);
const tasks = ref<Task[]>([]);
const editingTask = ref<Task | null>(null);

const isLoggedIn = ref(false);
const user = ref({ email: 'example@email.com' }); 
const logout = () => {
  isLoggedIn.value = false;
  user.value = { email: '' };
};

const handleLoginSuccess = (loggedInUser: { email: string }) => {
  isLoggedIn.value = true;
  user.value = loggedInUser;
  fetchTasks();
};

// Function to fetch tasks API
const fetchTasks = async () => {
  loading.value = true;
  error.value = null;
  try {
    tasks.value = await TaskApi.getAll();
  } catch (err: any) {
    error.value = err.message || 'Failed to fetch tasks.';
  } finally {
    loading.value = false;
  }
};

const toggleTask = async (id: number) => {
  const taskToUpdate = tasks.value.find(task => task.id === id);
  if (!taskToUpdate) return;
  
  const updatedTask = { ...taskToUpdate, completed: !taskToUpdate.completed };

  try {
    await TaskApi.update(id, { completed: updatedTask.completed });
    Object.assign(taskToUpdate, updatedTask);
  } catch (err: any) {
    error.value = err.message || `Failed to update task with ID: ${id}.`;
  }
};

const handleFormSubmit = async (taskData: TaskCreatePayload | TaskUpdatePayload) => {
  try {
    if (editingTask.value) {
      const updatedTask = await TaskApi.update(editingTask.value.id, taskData);
      const index = tasks.value.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) {
        tasks.value[index] = updatedTask;
      }
      editingTask.value = null; 
    } else {
      const newTask = await TaskApi.create(taskData as TaskCreatePayload);
      tasks.value.push(newTask);
    }
  } catch (err: any) {
    error.value = err.message || 'Failed to save task.';
  }
};

const editTask = (task: Task) => {
  editingTask.value = task;
};

const cancelEdit = () => {
  editingTask.value = null;
};

const deleteTask = async (id: number) => {
  try {
    await TaskApi.delete(id);
    tasks.value = tasks.value.filter(task => task.id !== id);
  } catch (err: any) {
    error.value = err.message || `Failed to delete task with ID: ${id}.`;
  }
};

onMounted(() => {
  // Do not fetch tasks on mount - wait for a successful login
});
</script>