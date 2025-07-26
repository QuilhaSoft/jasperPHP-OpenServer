import axios from 'axios';

const sanctumApi = axios.create({
  baseURL: 'http://localhost',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
});

sanctumApi.interceptors.request.use(config => {
  const xsrfToken = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
  if (xsrfToken) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrfToken.split('=')[1]);
  }
  return config;
});

export default sanctumApi;
