import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost/api', // Adjust if your backend URL is different
  
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

export default api;
