<template>
  <div class="dashboard-view">
    <div class="header">
      <h1>Your Tasks</h1>
      <div class="controls">
        <button @click="showCreateForm = !showCreateForm" class="btn-primary">
          {{ showCreateForm ? 'Cancel' : '+ New Task' }}
        </button>
        <div class="search-box">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search tasks..."
          >
          <span class="search-icon">🔍</span>
        </div>
      </div>
    </div>

    <TaskForm 
      v-if="showCreateForm" 
      @create="handleCreateTask" 
      class="task-form"
    />

    <div v-if="loading" class="loading-container">
      <div class="spinner"></div>
      <p>Loading your tasks...</p>
    </div>

    <div v-else-if="error" class="error-container">
      <div class="error-icon">⚠️</div>
      <h2>Couldn't load tasks</h2>
      <p>{{ error }}</p>
      <button @click="fetchTasks" class="retry-btn">Try Again</button>
    </div>

    <div v-else-if="filteredTasks.length === 0" class="empty-state">
      <div class="empty-icon">📭</div>
      <h2>No tasks found</h2>
      <p v-if="searchQuery">Your search didn't match any tasks</p>
      <p v-else>You don't have any tasks yet. Create your first task!</p>
      <button @click="showCreateForm = true" class="btn-primary">
        Create Your First Task
      </button>
    </div>

    <div v-else class="task-list">
      <div class="stats-bar">
        <div class="stat">
          <span class="number">{{ completedTasks }}</span>
          <span class="label">Completed</span>
        </div>
        <div class="stat">
          <span class="number">{{ pendingTasks }}</span>
          <span class="label">Pending</span>
        </div>
        <div class="stat">
          <span class="number">{{ totalTasks }}</span>
          <span class="label">Total</span>
        </div>
      </div>

      <div class="filters">
        <button 
          @click="filter = 'all'" 
          :class="{ active: filter === 'all' }"
        >
          All
        </button>
        <button 
          @click="filter = 'active'" 
          :class="{ active: filter === 'active' }"
        >
          Active
        </button>
        <button 
          @click="filter = 'completed'" 
          :class="{ active: filter === 'completed' }"
        >
          Completed
        </button>
      </div>

      <div class="tasks-container">
        <TaskItem 
          v-for="task in filteredTasks" 
          :key="task.id" 
          :task="task"
          @update="handleUpdateTask"
          @delete="handleDeleteTask"
        />
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import TodoForm from '../components/TodoForm.vue';
import Todoitem from '../components/Todoitem.vue';
import {Task} from '../types/interfaces';
import { TaskApi, handleApiError } from '../services/api';

export default defineComponent({
  name: 'DashboardView',
  components: { TodoForm, Todoitem },
  setup() {
    const tasks = ref<Task[]>([]);
    const loading = ref(true);
    const error = ref<string | null>(null);
    const showCreateForm = ref(false);
    const searchQuery = ref('');
    const filter = ref<'all' | 'active' | 'completed'>('all');

    // Fetch tasks from API
    const fetchTasks = async () => {
      loading.value = true;
      error.value = null;
      try {
        tasks.value = await TaskApi.getAll();
      } catch (err) {
        error.value = handleApiError(err, 'Failed to load tasks');
      } finally {
        loading.value = false;
      }
    };

    // Create a new task
    const handleCreateTask = async (title: string) => {
      try {
        const newTask = await TaskApi.create({ title, completed: false });
        tasks.value.unshift(newTask);
        showCreateForm.value = false;
      } catch (err) {
        error.value = handleApiError(err, 'Failed to create task');
      }
    };

    // Update existing task
    const handleUpdateTask = (updatedTask: Task) => {
      const index = tasks.value.findIndex(t => t.id === updatedTask.id);
      if (index !== -1) {
        tasks.value.splice(index, 1, updatedTask);
      }
    };

    // Delete task
    const handleDeleteTask = (id: number) => {
      tasks.value = tasks.value.filter(task => task.id !== id);
    };

    // Computed properties
    const filteredTasks = computed(() => {
      let result = [...tasks.value];
      
      // Apply filter
      if (filter.value === 'active') {
        result = result.filter(task => !task.completed);
      } else if (filter.value === 'completed') {
        result = result.filter(task => task.completed);
      }
      
      // Apply search
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(task => 
          task.title.toLowerCase().includes(query) || 
          (task.description && task.description.toLowerCase().includes(query))
        );
      }
      
      return result;
    });

    const totalTasks = computed(() => tasks.value.length);
    const completedTasks = computed(() => tasks.value.filter(t => t.completed).length);
    const pendingTasks = computed(() => totalTasks.value - completedTasks.value);

    // Initial data fetch
    onMounted(fetchTasks);

    return {
      tasks,
      loading,
      error,
      showCreateForm,
      searchQuery,
      filter,
      filteredTasks,
      totalTasks,
      completedTasks,
      pendingTasks,
      fetchTasks,
      handleCreateTask,
      handleUpdateTask,
      handleDeleteTask
    };
  }
});
</script>

<style scoped>
.dashboard-view {
  max-width: 800px;
  margin: 0 auto;
  padding: 1rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2rem;
  color: #2c3e50;
}

.controls {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-primary {
  background-color: #3498db;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #2980b9;
}

.search-box {
  position: relative;
}

.search-box input {
  padding: 0.75rem 1.5rem 0.75rem 2.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  width: 250px;
}

.search-icon {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #7f8c8d;
}

.task-form {
  margin-bottom: 2rem;
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid rgba(52, 152, 219, 0.2);
  border-top: 5px solid #3498db;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  text-align: center;
}

.error-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #e74c3c;
}

.retry-btn {
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background-color: #3498db;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 300px;
  text-align: center;
  padding: 2rem;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.7;
}

.stats-bar {
  display: flex;
  justify-content: space-around;
  background-color: white;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.number {
  font-size: 1.8rem;
  font-weight: 700;
  color: #3498db;
}

.label {
  font-size: 0.9rem;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filters {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.filters button {
  flex: 1;
  padding: 0.75rem;
  background: white;
  border: 1px solid #ddd;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.filters button:hover {
  background-color: #f1f8ff;
}

.filters button.active {
  background-color: #3498db;
  color: white;
  border-color: #3498db;
}

.tasks-container {
  display: grid;
  gap: 1rem;
}
</style>
