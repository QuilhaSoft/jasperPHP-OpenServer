import api from './api';

export default {
  getAllReports() {
    return api.get('/api/reports');
  },
  getReportById(id: string) {
    return api.get(`/api/reports/${id}`);
  },
  uploadReport(formData: FormData) {
    return api.post('/api/reports', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  updateReport(id: string, formData: FormData) {
    // For PUT/PATCH with FormData, some clients might need a specific header or method override
    // Axios handles this well, but if issues arise, check backend expectations for FormData updates.
    return api.post(`/api/reports/${id}?_method=PUT`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
  },
  deleteReport(id: string) {
    return api.delete(`/api/reports/${id}`);
  },
  executeReport(data: { report_id: string; data_source_id?: string; format: string; parameters?: object; json_data?: object }) {
    return api.post('/api/reports/execute', data, {
      responseType: 'blob', // Important for file downloads
    });
  },
};