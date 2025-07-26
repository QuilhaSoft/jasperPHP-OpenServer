<script setup lang="ts">
import { useDataSourceStore } from '@/stores/dataSourceStore';
import { useReportStore } from '@/stores/reportStore';
import { onMounted, ref, watch } from 'vue';

const dataSourceStore = useDataSourceStore();
const reportStore = useReportStore();

const selectedDataSourceId = ref<number | null>(null);
const reportName = ref('');
const reportData = ref('');

const executeReport = async () => {
  if (!selectedDataSourceId.value) {
    alert('Please select a data source.');
    return;
  }
  
  const selectedDs = dataSourceStore.dataSources.find(ds => ds.id === selectedDataSourceId.value);

  if (!selectedDs) {
    alert('Selected data source not found.');
    return;
  }

  const data: { report_id: string; data_source_id?: string; format: string; parameters?: object; json_data?: object } = {
    report_id: reportName.value, // Assuming reportName is the report_id
    data_source_id: selectedDs.id.toString(),
    format: 'pdf', // Default format, adjust as needed
  };

  if (selectedDs.type === 'json' || selectedDs.type === 'array') {
    try {
      data.json_data = JSON.parse(reportData.value);
    } catch (e) {
      alert('Invalid JSON data.');
      return;
    }
  }

  await reportStore.executeReport(data);
};

onMounted(() => {
  dataSourceStore.fetchDataSources();
});

watch(selectedDataSourceId, (newVal) => {
  if (newVal) {
    const selectedDs = dataSourceStore.dataSources.find(ds => ds.id === newVal);
    if (selectedDs && (selectedDs.type === 'json' || selectedDs.type === 'array')) {
      reportData.value = ''; // Clear data if not JSON/Array type
    }
  }
});
</script>

<template>
  <div>
    <h1>Execute Report</h1>
    <form @submit.prevent="executeReport">
      <div>
        <label for="dataSource">Data Source:</label>
        <select id="dataSource" v-model="selectedDataSourceId" required>
          <option :value="null" disabled>Select a data source</option>
          <option v-for="dataSource in dataSourceStore.dataSources" :key="dataSource.id" :value="dataSource.id">
            {{ dataSource.name }} ({{ dataSource.type }})
          </option>
        </select>
      </div>
      <div>
        <label for="reportName">Report Name:</label>
        <input type="text" id="reportName" v-model="reportName" required />
      </div>
      <div v-if="!selectedDataSourceId || (selectedDataSourceId && (dataSourceStore.dataSources.find(ds => ds.id === selectedDataSourceId)?.type === 'json' || dataSourceStore.dataSources.find(ds => ds.id === selectedDataSourceId)?.type === 'array'))">
        <label for="reportData">Report Data (JSON/XML):</label>
        <textarea id="reportData" v-model="reportData"></textarea>
      </div>
      <button type="submit">Execute Report</button>
    </form>

    <div v-if="reportStore.loading">Loading...</div>
    <div v-if="reportStore.error" class="error">{{ reportStore.error }}</div>
    <div v-if="reportStore.reportUrl">
      <a :href="reportStore.reportUrl" target="_blank">Download Report</a>
    </div>
  </div>
</template>

<style scoped>
.error {
  color: red;
}
</style>