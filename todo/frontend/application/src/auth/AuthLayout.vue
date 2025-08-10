<template>
  <div class="auth-layout">
    <div class="auth-card">
      <div class="brand-header">
        <!--INSERT LOGO IMAGE HERE FOR PROJECT-->
        <h1 class="app-name">Todo Manager</h1>
      </div>
      
      <div class="tabs">
        <button 
          @click="$emit('change-form', 'login')"
          :class="{ active: activeForm === 'login' }"
          class="tab-btn"
        >
          Sign In
        </button>
        <button 
          @click="$emit('change-form', 'register')"
          :class="{ active: activeForm === 'register' }"
          class="tab-btn"
        >
          Create Account
        </button>
      </div>
      
      <div class="form-container">
        <slot></slot>
      </div>
      
      <div class="divider">
        <span>or continue with</span>
      </div>
      
      <SocialAuth />
      
      <div class="legal-links">
        <router-link to="/privacy">Privacy Policy</router-link>
        <router-link to="/terms">Terms of Service</router-link>
      </div>
    </div>
    
    <footer class="auth-footer">
      <p>&copy; {{ new Date().getFullYear() }} Todo Manager. All rights reserved.</p>
    </footer>
  </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';
import SocialAuth from './SocialAuth.vue';

export default defineComponent({
  name: 'AuthLayout',
  components: { SocialAuth },
  props: {
    activeForm: {
      type: String as PropType<'login' | 'register'>,
      required: true
    }
  },
  setup() {
    return {  };
  }
});
</script>

<style scoped>
.auth-layout {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  padding: 2rem;
  background-color: #f5f7fa;
}

.auth-card {
  width: 100%;
  max-width: 450px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.brand-header {
  padding: 2rem;
  text-align: center;
  background: linear-gradient(135deg, #3498db, #2c3e50);
  color: white;
}

.logo {
  width: 80px;
  height: 80px;
  margin-bottom: 1rem;
}

.app-name {
  margin: 0;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.tabs {
  display: flex;
  border-bottom: 1px solid #eee;
}

.tab-btn {
  flex: 1;
  padding: 1.2rem;
  background: none;
  border: none;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-btn:hover {
  background-color: #f8f9fa;
}

.tab-btn.active {
  color: #3498db;
  border-bottom: 3px solid #3498db;
}

.form-container {
  padding: 2rem;
}

.divider {
  position: relative;
  text-align: center;
  margin: 1.5rem 2rem;
}

.divider::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #eee;
  z-index: 1;
}

.divider span {
  position: relative;
  padding: 0 1rem;
  background-color: white;
  color: #7f8c8d;
  z-index: 2;
}

.legal-links {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  padding: 1.5rem;
  font-size: 0.85rem;
}

.legal-links a {
  color: #7f8c8d;
  text-decoration: none;
  transition: color 0.2s;
}

.legal-links a:hover {
  color: #3498db;
}

.auth-footer {
  margin-top: 2rem;
  color: #7f8c8d;
  font-size: 0.9rem;
}
</style>
