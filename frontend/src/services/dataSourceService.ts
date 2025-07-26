import api from './api';

export default {
  getAllDataSources() {
    return api.get('/data-sources');
  },
  getDataSourceById(id: string) {
    return api.get(`/data-sources/${id}`);
  },
  createDataSource(data: { name: string; type: string; configuration: object }) {
    return api.post('/data-sources', data);
  },
  updateDataSource(id: string, data: { name: string; type: string; configuration: object }) {
    return api.put(`/data-sources/${id}`, data);
  },
  deleteDataSource(id: string) {
    return api.delete(`/data-sources/${id}`);
  },
};
