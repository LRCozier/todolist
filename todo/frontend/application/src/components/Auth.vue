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
import { ref, computed } from 'vue';

const isLoginMode = ref(true); // Toggles between login and registration forms
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

const handleLogin = () => {
  //Add api call to backend (login.php)
  console.log('Logging in with:', {
    username: loginUsername.value,
    password: loginPassword.value,
  });

  loginMessage.value = 'Login successful!';
  messageType.value = 'success';
};

const handleRegister = () => {
  // Add api call to backend (register.php)
  if (registerPassword.value !== confirmPassword.value) {
    registerMessage.value = 'Passwords do not match.';
    messageType.value = 'error';
    return;
  }

  console.log('Registering with:', {
    username: registerUsername.value,
    email: registerEmail.value,
    password: registerPassword.value,
  });
  
  registerMessage.value = 'Registration successful! You can now log in.';
  messageType.value = 'success';
};
</script>
