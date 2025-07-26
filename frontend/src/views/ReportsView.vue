<template>
  <div class="reports-view">
    <h1>Gerenciar Relatórios</h1>
    <!-- Formulário de upload de relatório -->
    <form @submit.prevent="uploadReport">
      <input type="text" v-model="newReport.name" placeholder="Nome do Relatório" required />
      <textarea v-model="newReport.description" placeholder="Descrição"></textarea>
      <input type="file" @change="handleFileUpload" accept=".jrxml,.jasper" required />
      <button type="submit">Upload Relatório</button>
    </form>

    <!-- Lista de relatórios -->
    <h2>Meus Relatórios</h2>
    <div v-if="reportStore.loading">Carregando relatórios...</div>
    <div v-else-if="reportStore.error">Erro: {{ reportStore.error }}</div>
    <ul v-else>
      <li v-for="report in reportStore.reports" :key="report.id">
        {{ report.name }} - {{ report.description }}
        <button @click="editReport(report)">Editar</button>
        <button @click="deleteReport(report.id)">Excluir</button>
        <button @click="executeReport(report.id)">Executar</button>
      </li>
    </ul>

    <!-- Formulário de edição (opcional, pode ser um modal) -->
    <div v-if="editingReport">
      <h2>Editar Relatório</h2>
      <form @submit.prevent="saveEditedReport">
        <input type="text" v-model="editingReport.name" required />
        <textarea v-model="editingReport.description"></textarea>
        <input type="file" @change="handleEditFileUpload" accept=".jrxml,.jasper" />
        <button type="submit">Salvar</button>
        <button @click="cancelEdit">Cancelar</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useReportStore } from '../stores/reportStore';
import { useRouter } from 'vue-router';

const reportStore = useReportStore();
const router = useRouter();

const newReport = ref({
  name: '',
  description: '',
});
const selectedFile = ref<File | null>(null);

const editingReport = ref<any | null>(null);
const editedFile = ref<File | null>(null);

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    selectedFile.value = target.files[0];
  }
};

const handleEditFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    editedFile.value = target.files[0];
  }
};

const uploadReport = async () => {
  if (!selectedFile.value) return;

  const formData = new FormData();
  formData.append('name', newReport.value.name);
  formData.append('description', newReport.value.description);
  formData.append('report_file', selectedFile.value);

  await reportStore.uploadReport(formData);
  newReport.value = { name: '', description: '' };
  selectedFile.value = null;
  await reportStore.fetchReports(); // Refresh list
};

const editReport = (report: any) => {
  editingReport.value = { ...report };
  editedFile.value = null;
};

const saveEditedReport = async () => {
  if (!editingReport.value) return;

  const formData = new FormData();
  formData.append('name', editingReport.value.name);
  formData.append('description', editingReport.value.description);
  if (editedFile.value) {
    formData.append('report_file', editedFile.value);
  }

  await reportStore.updateReport(editingReport.value.id, formData);
  editingReport.value = null;
  editedFile.value = null;
  await reportStore.fetchReports(); // Refresh list
};

const cancelEdit = () => {
  editingReport.value = null;
  editedFile.value = null;
};

const deleteReport = async (id: string) => {
  if (confirm('Tem certeza que deseja excluir este relatório?')) {
    await reportStore.deleteReport(id);
    await reportStore.fetchReports(); // Refresh list
  }
};

const executeReport = (reportId: string) => {
  router.push({ name: 'execute-report', query: { reportId } });
};

onMounted(() => {
  reportStore.fetchReports();
});
</script>

<style scoped>
.reports-view {
  padding: 20px;
}
form {
  margin-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 400px;
}
input[type="text"], textarea, input[type="file"] {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
button {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
button:hover {
  background-color: #0056b3;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  background-color: #f9f9f9;
  border: 1px solid #eee;
  padding: 10px;
  margin-bottom: 8px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
li button {
  margin-left: 10px;
  padding: 5px 10px;
  font-size: 0.8em;
}
</style>
