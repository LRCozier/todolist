<template>
  <div class="not-found-view">
    <div class="error-code">404</div>
    <h1>Page Not Found</h1>
    <p>Oops! The page you're looking for doesn't exist.</p>
    <div class="actions">
      <router-link to="/" class="home-link">Go to Home</router-link>
      <button @click="goBack" class="back-btn">Go Back</button>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'NotFoundView',
  setup() {
    const router = useRouter();
    
    const goBack = () => {
      router.go(-1);
    };
    
    return { goBack };
  }
});
</script>

<style scoped>
.not-found-view {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 80vh;
  text-align: center;
  padding: 2rem;
}

.error-code {
  font-size: 8rem;
  font-weight: 700;
  color: #3498db;
  line-height: 1;
  margin-bottom: 1rem;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
  color: #2c3e50;
}

p {
  font-size: 1.25rem;
  color: #7f8c8d;
  margin-bottom: 2rem;
}

.actions {
  display: flex;
  gap: 1rem;
}

.home-link, .back-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s;
}

.home-link {
  background-color: #3498db;
  color: white;
  border: none;
}

.home-link:hover {
  background-color: #2980b9;
}

.back-btn {
  background: none;
  border: 1px solid #3498db;
  color: #3498db;
}

.back-btn:hover {
  background-color: #f1f8ff;
}
</style>
