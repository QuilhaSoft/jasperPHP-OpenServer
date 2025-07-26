import { defineStore } from 'pinia';
import sanctumApi from '../services/sanctumApi';
import api from '../services/api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    isAuthenticated: false,
    loading: false,
    error: null,
  }),
  actions: {
    async getCsrfCookie() {
      await sanctumApi.get('/sanctum/csrf-cookie');
    },
    async login(credentials:any) {
      this.loading = true;
      this.error = null;
      try {
        await this.getCsrfCookie();
        const response = await sanctumApi.post('/api/login', credentials);
        this.user = response.data.user;
        this.isAuthenticated = true;
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Login failed';
        this.isAuthenticated = false;
        throw e;
      } finally {
        this.loading = false;
      }
    },
    async register(credentials: any) {
      this.loading = true;
      this.error = null;
      try {
        await this.getCsrfCookie();
        const response = await sanctumApi.post('/api/register', credentials);
        this.user = response.data.user;
        this.isAuthenticated = true;
      } catch (e: any) {
        this.error = e.response?.data || { message: 'Registration failed' };
        this.isAuthenticated = false;
        throw e;
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      this.loading = true;
      try {
        await api.post('/api/logout');
        this.user = null;
        this.isAuthenticated = false;
      } finally {
        this.loading = false;
      }
    },
    async fetchUser() {
      this.loading = true;
      try {
        const response = await api.get('/api/user');
        this.user = response.data;
        this.isAuthenticated = true;
      } catch {
        this.user = null;
        this.isAuthenticated = false;
      } finally {
        this.loading = false;
      }
    },
  },
});