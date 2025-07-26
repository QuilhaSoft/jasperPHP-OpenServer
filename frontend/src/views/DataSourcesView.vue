<script setup lang="ts">
import { useDataSourceStore } from '@/stores/dataSourceStore';
import { onMounted, ref } from 'vue';

const dataSourceStore = useDataSourceStore();

const newDataSource = ref({
  name: '',
  type: '',
});

const addDataSource = async () => {
  try {
    await dataSourceStore.addDataSource(newDataSource.value);
    newDataSource.value = { name: '', type: '' };
  } catch (error) {
    console.error('Error adding data source:', error);
  }
};

const deleteDataSource = async (id: number) => {
  try {
    await dataSourceStore.deleteDataSource(id);
  } catch (error) {
    console.error('Error deleting data source:', error);
  }
};

onMounted(() => {
  dataSourceStore.fetchDataSources();
});
</script>

<template>
  <div>
    <h1>Data Sources</h1>
    <form @submit.prevent="addDataSource">
      <input type="text" v-model="newDataSource.name" placeholder="Name" required />
      <input type="text" v-model="newDataSource.type" placeholder="Type" required />
      <button type="submit">Add Data Source</button>
    </form>

    <ul>
      <li v-for="dataSource in dataSourceStore.dataSources" :key="dataSource.id">
        {{ dataSource.name }} ({{ dataSource.type }})
        <button @click="deleteDataSource(dataSource.id)">Excluir</button>
      </li>
    </ul>
  </div>
</template>