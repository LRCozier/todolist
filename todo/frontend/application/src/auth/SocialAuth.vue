<template>
  <div class="social-auth">
    <button type="button" class="social-btn google" @click="signInWithGoogle">
      <font-awesome-icon icon="fa-brands fa-google" alt="Google" class="social-icon" />
      Continue with Google
    </button>
    
    <button type="button" class="social-btn github" @click="signInWithGithub">
      <font-awesome-icon icon="fa-brands fa-github" alt="Github" class="social-icon" />
      Continue with GitHub
    </button>
    
    <button type="button" class="social-btn microsoft" @click="signInWithMicrosoft">
      <font-awesome-icon icon="fa-brands fa-microsoft" alt="Microsoft" class="social-icon" />
      Continue with Microsoft
    </button>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { AuthService } from '../services/api';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

import { 
  faMicrosoft, 
  faGoogle, 
  faGithub 
} from '@fortawesome/free-brands-svg-icons';

library.add(faMicrosoft, faGoogle, faGithub);

export default defineComponent({
  name: 'SocialAuth',
  setup() {
    const signInWithGoogle = () => {
      AuthService.socialLogin('google');
    };
    
    const signInWithGithub = () => {
      AuthService.socialLogin('github');
    };
    
    const signInWithMicrosoft = () => {
      AuthService.socialLogin('microsoft');
    };
    
    return {
      signInWithGoogle,
      signInWithGithub,
      signInWithMicrosoft
    };
  }
});
</script>

<style scoped>
.social-auth {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding: 0 2rem 2rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  background: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.social-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.social-btn.google {
  color: #5f6368;
}

.social-btn.google:hover {
  background-color: #f8f9fa;
}

.social-btn.github {
  color: #24292e;
}

.social-btn.github:hover {
  background-color: #f6f8fa;
}

.social-btn.microsoft {
  color: #5e5e5e;
}

.social-btn.microsoft:hover {
  background-color: #f5f5f5;
}

.social-icon {
  width: 20px;
  height: 20px;
}
</style>
