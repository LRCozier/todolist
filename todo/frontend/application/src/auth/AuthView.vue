<template>
  <AuthLayout :active-form="activeForm" @change-form="changeActiveForm">
    <LoginForm 
      v-if="activeForm === 'login'" 
      @change-form="changeActiveForm"
    />
    <RegistrationForm 
      v-else 
      @change-form="changeActiveForm"
    />
  </AuthLayout>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useRoute } from 'vue-router';
import AuthLayout from './AuthLayout.vue'
import LoginForm from './LoginForm.vue';
import RegistrationForm from './Register.vue';

export default defineComponent({
  name: 'AuthView',
  components: {
    AuthLayout,
    LoginForm,
    RegistrationForm
  },
  setup() {
    const route = useRoute();
    const activeForm = ref<'login' | 'register'>(
      route.query.form === 'register' ? 'register' : 'login'
    );
    
    const changeActiveForm = (form: 'login' | 'register') => {
      activeForm.value = form;
    };
    
    return {
      activeForm,
      changeActiveForm
    };
  }
});
</script>
