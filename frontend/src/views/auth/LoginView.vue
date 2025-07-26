<template>
  <div class="login">
    <h1>Login</h1>
    <form @submit.prevent="handleLogin">
      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <button type="submit">Login</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import sanctumApi from '@/services/sanctumApi';

const email = ref('');
const password = ref('');
const error = ref<string | null>(null);
const router = useRouter();

const handleLogin = async () => {
  error.value = null;
  try {
    // Get CSRF token
    await sanctumApi.get('/sanctum/csrf-cookie');

    // Attempt login
    await api.post('/api/login', {
      email: email.value,
      password: password.value,
    });

    // Redirect to home or dashboard
    router.push('/');
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Login failed';
  }
};
</script>

<style scoped>
.login {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

form div {
  margin-bottom: 1rem;
}

.error {
  color: red;
  margin-top: 1rem;
}
</style>
