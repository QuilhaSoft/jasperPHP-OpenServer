<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const error = ref<AuthError | null>(null);

const register = async () => {
  try {
    await authStore.getCsrfCookie();
    await authStore.register(form.value);
    router.push('/');
  } catch (e: any) {
    error.value = e.response?.data || { message: 'Registration failed' };
  }
};
</script>

<template>
  <div>
    <h1>Register</h1>
    <form @submit.prevent="register">
      <div>
        <label for="name">Name:</label>
        <input type="text" id="name" v-model="form.name" required />
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="form.email" required />
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="form.password" required />
      </div>
      <div>
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" id="password_confirmation" v-model="form.password_confirmation" required />
      </div>
      <button type="submit" :disabled="authStore.loading">Register</button>
    </form>

    <div v-if="error" class="error-messages">
      <p v-if="error.message">{{ error.message }}</p>
      <ul v-if="error.errors">
        <li v-for="(messages, field) in error.errors" :key="field">
          {{ field }}: {{ messages.join(', ') }}
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.error-messages {
  color: red;
  margin-top: 10px;
}
</style>