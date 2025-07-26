import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/authStore'
import sanctumApi from './services/sanctumApi';

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Fetch CSRF cookie on app initialization
router.isReady().then(() => {
  sanctumApi.get('/sanctum/csrf-cookie').then(() => {
    const authStore = useAuthStore();
    authStore.fetchUser();
  });
});

app.mount('#app')