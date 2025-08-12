<template>
  <AuthLayout>
    <div class="register-container">
      <h2>Create Your Account</h2>
      <p class="subtitle">Start organizing your tasks today</p>
      
      <form @submit.prevent="submitForm" class="register-form">
        <div class="form-row">
          <div class="form-group">
            <label for="firstName">First Name</label>
            <input 
              type="text" 
              id="firstName" 
              v-model="firstName"
              :class="{ 'input-error': errors.firstName }"
              placeholder="John"
              autocomplete="given-name"
              required
            >
            <div v-if="errors.firstName" class="error-message">{{ errors.firstName }}</div>
          </div>
          
          <div class="form-group">
            <label for="lastName">Last Name</label>
            <input 
              type="text" 
              id="lastName" 
              v-model="lastName"
              :class="{ 'input-error': errors.lastName }"
              placeholder="Doe"
              autocomplete="family-name"
              required
            >
            <div v-if="errors.lastName" class="error-message">{{ errors.lastName }}</div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="email">Email Address</label>
          <input 
            type="email" 
            id="email" 
            v-model="email"
            :class="{ 'input-error': errors.email }"
            placeholder="your@email.com"
            autocomplete="email"
            required
          >
          <div v-if="errors.email" class="error-message">{{ errors.email }}</div>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            :type="showPassword ? 'text' : 'password'" 
            id="password" 
            v-model="password"
            :class="{ 'input-error': errors.password }"
            placeholder="••••••••"
            autocomplete="new-password"
            required
          >
          <button 
            type="button" 
            class="password-toggle"
            @click="showPassword = !showPassword"
          >
            {{ showPassword ? 'Hide' : 'Show' }}
          </button>
          <div class="password-hint">
            Must be at least 8 characters with a number and symbol
          </div>
          <div v-if="errors.password" class="error-message">{{ errors.password }}</div>
        </div>
        
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input 
            :type="showConfirmPassword ? 'text' : 'password'" 
            id="confirmPassword" 
            v-model="confirmPassword"
            :class="{ 'input-error': errors.confirmPassword }"
            placeholder="••••••••"
            autocomplete="new-password"
            required
          >
          <button 
            type="button" 
            class="password-toggle"
            @click="showConfirmPassword = !showConfirmPassword"
          >
            {{ showConfirmPassword ? 'Hide' : 'Show' }}
          </button>
          <div v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</div>
        </div>
        
        <div class="terms-agreement">
          <input 
            type="checkbox" 
            id="terms" 
            v-model="acceptedTerms"
            :class="{ 'input-error': errors.acceptedTerms }"
          >
          <label for="terms">
            I agree to the <router-link to="/terms">Terms of Service</router-link> and 
            <router-link to="/privacy">Privacy Policy</router-link>
          </label>
          <div v-if="errors.acceptedTerms" class="error-message">{{ errors.acceptedTerms }}</div>
        </div>
        
        <button 
          type="submit" 
          class="submit-btn"
          :disabled="isSubmitting" >
          <span v-if="isSubmitting" class="spinner"></span> <span v-else>Create Account</span>
        </button>
        
        <div v-if="authError" class="auth-error"> <div class="error-icon">!</div>
          {{ authError }}
        </div>
      </form>
      
      <div class="divider">
        <span>or continue with</span>
      </div>
      
      <SocialAuth />
      
      <div class="login-link">
        Already have an account? 
        <router-link to="/login">Sign in</router-link>
      </div>
    </div>
  </AuthLayout>
</template>

<script lang="ts">
import { defineComponent, ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import AuthLayout from '../auth/AuthLayout.vue';
import SocialAuth from '../auth/SocialAuth.vue';
import { AuthApi } from '../services/api'; 
import { useAuthStore } from '/stores/auth';

export default defineComponent({
  name: 'RegisterView',
  components: { AuthLayout, SocialAuth },
  setup() {
    const router = useRouter();
    const authStore = useAuthStore();
    const firstName = ref('');
    const lastName = ref('');
    const email = ref('');
    const password = ref('');
    const confirmPassword = ref('');
    const acceptedTerms = ref(false);
    const showPassword = ref(false);
    const showConfirmPassword = ref(false);
    const isSubmitting = ref(false);
    const authError = ref<string | null>(null);
    
    const errors = reactive({
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      confirmPassword: '',
      acceptedTerms: ''
    });
    
    const validateForm = () => {
      let isValid = true;
      Object.keys(errors).forEach(key => { errors[key as keyof typeof errors] = ''; });
      
      if (!firstName.value) { 
      errors.firstName = 'First name is required'; 
      isValid = false; 
      }

      if (!lastName.value) { 
        errors.lastName = 'Last name is required'; 
        isValid = false; 
      }

      if (!email.value) { 
        errors.email = 'Email is required'; 
        isValid = false; 
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { 
        errors.email = 'Please enter a valid email address'; 
        isValid = false; 
      }

      if (!password.value) { 
        errors.password = 'Password is required'; 
        isValid = false;
      } else if (password.value.length < 8) { 
        errors.password = 'Password must be at least 8 characters'; 
        isValid = false; 
      } else if (!/[0-9]/.test(password.value)) 
      { errors.password = 'Password must contain at least one number'; 
        isValid = false; 
      } else if (!/[!@#$%^&*]/.test(password.value)) { 
        errors.password = 'Password must contain at least one special character'; 
        isValid = false; 
      }

      if (password.value !== confirmPassword.value) {
        errors.confirmPassword = 'Passwords do not match'; 
        isValid = false; 
      }

      if (!acceptedTerms.value) {
        errors.acceptedTerms = 'You must accept the terms to continue'; 
        isValid = false; 
      }
      
      return isValid;
    };
    
    // REPLACED: This is the combined and improved submission logic
    const submitForm = async () => {
      if (!validateForm()) return;
      
      isSubmitting.value = true;
      authError.value = null;
      
      try {
        const token = await AuthApi.register({
          firstName: firstName.value,
          lastName: lastName.value,
          email: email.value,
          password: password.value
        });
        
       
        authStore.login(token, true);
        
       
        router.push('/dashboard');
      } catch (err) {
        
        if (err instanceof Error && err.message.includes('already exists')) {
          authError.value = 'An account with this email already exists';
        } else {
          authError.value = 'Registration failed. Please try again.';
        }
        console.error('Registration error:', err);
      } finally {
        isSubmitting.value = false;
      }
    };
    
    return {
      firstName,
      lastName,
      email,
      password,
      confirmPassword,
      acceptedTerms,
      showPassword,
      showConfirmPassword,
      isSubmitting,
      authError,
      errors,
      submitForm 
    };
  }
});
</script>

<style scoped>

.register-container {
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
  padding: 2rem;
}

h2 {
  text-align: center;
  margin-bottom: 0.5rem;
  color: #2c3e50;
}

.subtitle {
  text-align: center;
  color: #7f8c8d;
  margin-bottom: 2rem;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-row > .form-group {
  flex: 1;
}

.password-hint {
  margin-top: 0.4rem;
  color: #7f8c8d;
  font-size: 0.85rem;
}

.terms-agreement {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.terms-agreement label {
  font-size: 0.95rem;
  font-weight: normal;
  line-height: 1.4;
}

.terms-agreement a {
  color: #3498db;
  text-decoration: none;
}

.terms-agreement a:hover {
  text-decoration: underline;
}

.login-link {
  text-align: center;
  margin-top: 1.5rem;
  color: #7f8c8d;
}

.login-link a {
  color: #3498db;
  font-weight: 500;
  text-decoration: none;
}

.login-link a:hover {
  text-decoration: underline;
}

.form-group { 
  position: relative; 
}

label { 
  display: block; 
  margin-bottom: 0.5rem; 
  font-weight: 500; 
  color: #2c3e50; 
}

input { 
  width: 100%; 
  padding: 0.9rem; 
  border: 1px solid #ddd; 
  border-radius: 6px; font-size: 1rem; 
  transition: all 0.2s; 
}

input:focus { 
  outline: none; 
  border-color: #3498db; 
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2); 
}

.input-error { 
  border-color: #e74c3c; 
}

.input-error:focus { 
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.2); 
}

.error-message { 
  margin-top: 0.4rem; 
  color: #e74c3c; 
  font-size: 0.85rem; 
}

.password-toggle { 
  position: absolute; 
  right: 10px; 
  top: 42px; 
  background: none; 
  border: none; 
  color: #7f8c8d; 
  font-size: 0.85rem; 
  cursor: pointer; 
}

.submit-btn { 
  padding: 1rem; 
  background-color: #3498db; 
  color: white; 
  border: none; 
  border-radius: 6px; 
  font-size: 1rem; 
  font-weight: 600; 
  cursor: pointer; 
  transition: background-color 0.2s; 
  height: 48px; 
}

.submit-btn:hover { 
  background-color: #2980b9; 
}

.submit-btn:disabled { 
  background-color: #bdc3c7; 
  cursor: not-allowed; 
}

.spinner { 
  display: inline-block; 
  width: 20px; height: 20px; 
  border: 3px solid rgba(255,255,255,0.3); 
  border-radius: 50%; 
  border-top-color: white; 
  animation: spin 1s ease-in-out infinite; 
}

@keyframes spin {
   to { 
    transform: rotate(360deg); 
  } 
}

.auth-error { 
  display: flex; 
  align-items: center; 
  gap: 0.75rem; 
  padding: 1rem; 
  background-color: #ffeef0; 
  color: #e74c3c; 
  border-radius: 6px; 
  font-weight: 500; 
}

.error-icon { 
  display: flex; 
  align-items: center; 
  justify-content: center; 
  width: 24px; 
  height: 24px; 
  background-color: #e74c3c; 
  color: white; 
  border-radius: 50%; 
  font-weight: bold; 
}

</style>