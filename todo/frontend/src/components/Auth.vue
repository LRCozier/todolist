<template>
  <div class="auth-container">
    <div class="form-card">
      <form v-if="isLoginMode" @submit.prevent="handleLogin" class="auth-form">
        <h2>Login</h2>
        <p v-if="loginMessage" :class="messageClass">{{ loginMessage }}</p>

        <div class="input-group">
          <label for="login-username">Username or Email</label>
          <input
            type="text"
            id="login-username"
            v-model="loginUsername"
            required
            autocomplete="username"
          />
        </div>

        <div class="input-group">
          <label for="login-password">Password</label>
          <input
            :type="showPassword ? 'text' : 'password'"
            id="login-password"
            v-model="loginPassword"
            required
            autocomplete="current-password"
          />
        </div>

        <div class="input-group-inline">
          <input
            type="checkbox"
            id="show-password"
            v-model="showPassword"
          />
          <label for="show-password">Show Password</label>
        </div>

        <button type="submit" class="btn-primary">Login</button>

        <div class="form-footer">
          <a href="#" class="forgot-password">Forgot Password?</a>
          <span>&middot;</span>
          <a @click.prevent="isLoginMode = false" href="#" class="forgot-password">Don't have an account? Sign Up</a>
        </div>
      </form>

      <form v-else @submit.prevent="handleRegister" class="auth-form">
        <h2>Create Account</h2>

        <p v-if="registerMessage" :class="messageClass">{{ registerMessage }}</p>

        <div class="input-group">
          <label for="register-username">Username</label>
          <input
            type="text"
            id="register-username"
            v-model="registerUsername"
            required
          />
        </div>

        <div class="input-group">
          <label for="register-email">Email</label>
          <input
            type="email"
            id="register-email"
            v-model="registerEmail"
            required
          />
        </div>

        <div class="input-group">
          <label for="register-password">Password</label>
          <input
            type="password"
            id="register-password"
            v-model="registerPassword"
            required
          />
        </div>

        <div class="input-group">
          <label for="confirm-password">Confirm Password</label>
          <input
            type="password"
            id="confirm-password"
            v-model="confirmPassword"
            required
          />
        </div>

        <button type="submit" class="btn-primary">Register</button>

        <div class="form-footer">
          <a @click.prevent="isLoginMode = true" href="#" class="forgot-password">Already have an account? Login</a>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { AuthApi } from '../services/api';

const emit = defineEmits(['login-success']);

const isLoginMode = ref(true);
const showPassword = ref(false);

const loginUsername = ref('');
const loginPassword = ref('');
const loginMessage = ref('');
const messageType = ref('');

const registerUsername = ref('');
const registerEmail = ref('');
const registerPassword = ref('');
const confirmPassword = ref('');
const registerMessage = ref('');

const messageClass = computed(() => {
  return messageType.value === 'success' ? 'success-message' : 'error-message';
});

watch([loginUsername, loginPassword, registerUsername, registerEmail, registerPassword, confirmPassword], () => {
  loginMessage.value = '';
  registerMessage.value = '';
});

const handleLogin = async () => {
  try {
    const response = await AuthApi.login({
      username: loginUsername.value,
      password: loginPassword.value,
    });

    if (response.success) {
      loginMessage.value = response.message || 'Login successful!';
      messageType.value = 'success';
      emit('login-success', { email: response.user?.email,
        username: response.user?.username,
        token: response.token
       });
    } else {
      loginMessage.value = response.error || 'Login failed. Please try again.';
      messageType.value = 'error';
    }
  } catch (error: any) {
    loginMessage.value = error.message || 'An error occurred during login.';
    messageType.value = 'error';
  }
};

const handleRegister = async () => {
  if (registerPassword.value !== confirmPassword.value) {
    registerMessage.value = 'Passwords do not match.';
    messageType.value = 'error';
    return;
  }

  try {
    const response = await AuthApi.register({
      username: registerUsername.value,
      email: registerEmail.value,
      password: registerPassword.value,
      confirm_password: confirmPassword.value,
    });

    if (response.success) {
      registerMessage.value = response.message || 'Registration successful! You can now log in.';
      messageType.value = 'success';
      isLoginMode.value = true;
    } else {
      registerMessage.value = response.error || 'Registration failed. Please try again.';
      messageType.value = 'error';
    }
  } catch (error: any) {
    registerMessage.value = error.message || 'An error occurred during registration.';
    messageType.value = 'error';
  }
};
</script>
