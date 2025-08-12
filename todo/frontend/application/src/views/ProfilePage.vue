<template>
  <div class="profile-view">
    <div class="header">
      <h1>Your Profile</h1>
      <p>Manage your account settings</p>
    </div>
    
    <div class="profile-card">
      <div class="profile-header">
        <div class="avatar">
          <span>{{ userInitials }}</span>
        </div>
        <div class="user-info">
          <h2>{{ user.email }}</h2>
          <p>Member since {{ formattedJoinDate }}</p>
        </div>
      </div>
      
      <div class="profile-stats">
        <div class="stat">
          <span class="number">{{ stats.totalTasks }}</span>
          <span class="label">Total Tasks</span>
        </div>
        <div class="stat">
          <span class="number">{{ stats.completedTasks }}</span>
          <span class="label">Completed</span>
        </div>
        <div class="stat">
          <span class="number">{{ stats.activeTasks }}</span>
          <span class="label">Active</span>
        </div>
      </div>
      
      <div class="profile-actions">
        <div class="action-card">
          <h3>Account Settings</h3>
          <button @click="changePassword" class="action-btn">
            Change Password
          </button>
          <button @click="updateEmail" class="action-btn">
            Update Email
          </button>
        </div>
        
        <div class="action-card danger">
          <h3>Danger Zone</h3>
          <button @click="deleteAccount" class="action-btn danger">
            Delete Account
          </button>
          <p class="warning">This action cannot be undone</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { AuthApi, StatsApi, handleApiError } from '../services/api';

export default defineComponent({
  name: 'ProfileView',
  setup() {
    const router = useRouter();
    const user = ref({
      email: '',
      createdAt: ''
    });
    const stats = ref({
      totalTasks: 0,
      completedTasks: 0,
      activeTasks: 0
    });
    const loading = ref(true);
    const error = ref<string | null>(null);

    // Compute user initials for avatar
    const userInitials = computed(() => {
      if (!user.value.email) return '';
      const nameParts = user.value.email.split('@')[0].split(/[._]/);
      return nameParts
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .substring(0, 2);
    });

    // Format join date
    const formattedJoinDate = computed(() => {
      if (!user.value.createdAt) return '';
      return new Date(user.value.createdAt).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    });

    // Fetch user data
    const fetchUserData = async () => {
      loading.value = true;
      try {
        const userData = await AuthApi.getCurrentUser();
        user.value = userData;
        
        const statsData = await StatsApi.getTaskStats();
        stats.value = statsData;
      } catch (err) {
        error.value = handleApiError(err, 'Failed to load profile data');
      } finally {
        loading.value = false;
      }
    };

    const changePassword = () => {
      // Implement password change flow
      alert('Password change functionality coming soon!');
    };

    const updateEmail = () => {
      // Implement email update flow
      alert('Email update functionality coming soon!');
    };

    const deleteAccount = () => {
      if (confirm('Are you sure you want to delete your account? This cannot be undone.')) {
        // Implement account deletion
        alert('Account deletion functionality coming soon!');
      }
    };

    onMounted(fetchUserData);

    return {
      user,
      stats,
      loading,
      error,
      userInitials,
      formattedJoinDate,
      changePassword,
      updateEmail,
      deleteAccount
    };
  }
});
</script>

<style scoped>
.profile-view {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.header {
  margin-bottom: 2rem;
}

.header h1 {
  font-size: 2rem;
  color: #2c3e50;
}

.header p {
  color: #7f8c8d;
}

.profile-card {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  padding: 2rem;
  background: linear-gradient(135deg, #3498db, #2c3e50);
  color: white;
}

.avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  margin-right: 1.5rem;
}

.user-info h2 {
  margin: 0;
  font-size: 1.5rem;
}

.profile-stats {
  display: flex;
  justify-content: space-around;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.stat .number {
  font-size: 1.8rem;
  font-weight: 700;
  color: #3498db;
}

.stat .label {
  font-size: 0.9rem;
  color: #7f8c8d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.profile-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  padding: 2rem;
}

.action-card {
  padding: 1.5rem;
  border: 1px solid #eee;
  border-radius: 8px;
}

.action-card.danger {
  border-color: #ffdddd;
  background-color: #fff8f8;
}

.action-card h3 {
  margin-top: 0;
  color: #2c3e50;
}

.action-btn {
  display: block;
  width: 100%;
  padding: 0.75rem;
  margin-bottom: 0.75rem;
  background-color: #f1f8ff;
  border: 1px solid #d1e7ff;
  border-radius: 4px;
  color: #3498db;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background-color: #d1e7ff;
}

.action-btn.danger {
  background-color: #ffebee;
  border-color: #ffcdd2;
  color: #e74c3c;
}

.action-btn.danger:hover {
  background-color: #ffcdd2;
}

.warning {
  color: #e74c3c;
  font-size: 0.9rem;
  margin-top: 0.5rem;
}
</style>
