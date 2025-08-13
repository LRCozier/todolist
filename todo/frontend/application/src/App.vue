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
    
    <div v-else>
      <TodoForm
        :initial-task="editingTask"
        @submit-task="handleFormSubmit"
        @cancel="cancelEdit"
      />
      
      <TodoList
        v-if="user"
        :tasks="tasks"
        @toggle="toggleTask"
        @edit="editTask"
        @delete="deleteTask"
      />
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
import { TaskApi } from './services/api';
import { Task, TaskCreatePayload, TaskUpdatePayload } from './types/interfaces';

// State management
const loading = ref(true);
const error = ref<string | null>(null);
const tasks = ref<Task[]>([]);
const editingTask = ref<Task | null>(null); // State to hold the task being edited

// User data (placeholder for a real authentication system)
const user = ref({ email: 'example@email.com' }); 
const logout = () => {
  // Placeholder for logout logic
  console.log('Logging out...');
};

// Function to fetch tasks from the API
const fetchTasks = async () => {
  loading.value = true;
  error.value = null; // Reset error
  try {
    tasks.value = await TaskApi.getAll();
  } catch (err: any) {
    error.value = err.message || 'Failed to fetch tasks.';
  } finally {
    loading.value = false;
  }
};

// Handlers for events from child components
const toggleTask = async (id: number) => {
  const taskToUpdate = tasks.value.find(task => task.id === id);
  if (!taskToUpdate) return;
  
  const updatedTask = { ...taskToUpdate, completed: !taskToUpdate.completed };

  try {
    await TaskApi.update(id, { completed: updatedTask.completed });
    // Update the task list without re-fetching everything
    Object.assign(taskToUpdate, updatedTask);
  } catch (err: any) {
    error.value = err.message || `Failed to update task with ID: ${id}.`;
  }
};

const handleFormSubmit = async (taskData: TaskCreatePayload | TaskUpdatePayload) => {
  try {
    if (editingTask.value) {
      // It's an update operation
      const updatedTask = await TaskApi.update(editingTask.value.id, taskData);
      
      // Find the old task in the array and replace it with the updated one
      const index = tasks.value.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) {
        tasks.value[index] = updatedTask;
      }
      
      editingTask.value = null; // Clear editing state
    } else {
      // It's a create operation
      const newTask = await TaskApi.create(taskData as TaskCreatePayload);
      tasks.value.push(newTask);
    }
  } catch (err: any) {
    error.value = err.message || 'Failed to save task.';
  }
};

const editTask = (task: Task) => {
  editingTask.value = task; // Set the task to be edited
};

const cancelEdit = () => {
  editingTask.value = null; // Cancel the editing state
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
  fetchTasks();
});
</script>

<style scoped>
.app {
  padding: 20px;
}
.error {
  color: red;
  font-weight: bold;
}
</style>