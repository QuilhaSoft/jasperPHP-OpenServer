import { defineStore } from 'pinia';
import api from '../services/api';

export const useDataSourceStore = defineStore('dataSource', {
  state: () => ({
    dataSources: [] as DataSource[],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchDataSources() {
      this.loading = true;
      try {
        const response = await api.get('/data-sources');
        this.dataSources = response.data;
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Failed to fetch data sources';
      } finally {
        this.loading = false;
      }
    },
    async addDataSource(dataSource: NewDataSource) {
      this.loading = true;
      try {
        const response = await api.post('/data-sources', dataSource);
        this.dataSources.push(response.data);
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Failed to add data source';
        throw e;
      } finally {
        this.loading = false;
      }
    },
    async updateDataSource(id: number, dataSource: DataSource) {
      this.loading = true;
      try {
        const response = await api.put(`/data-sources/${id}`, dataSource);
        const index = this.dataSources.findIndex(ds => ds.id === id);
        if (index !== -1) {
          this.dataSources[index] = response.data;
        }
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Failed to update data source';
        throw e;
      } finally {
        this.loading = false;
      }
    },
    async deleteDataSource(id: number) {
      this.loading = true;
      try {
        await api.delete(`/data-sources/${id}`);
        this.dataSources = this.dataSources.filter(ds => ds.id !== id);
      } catch (e: any) {
        this.error = e.response?.data?.message || 'Failed to delete data source';
        throw e;
      } finally {
        this.loading = false;
      }
    },
  },
});
