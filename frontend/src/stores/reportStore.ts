import { defineStore } from 'pinia';
import reportService from '../services/reportService';

interface Report {
  id: string;
  name: string;
  description: string;
  file_path: string;
  user_id: string;
  created_at: string;
  updated_at: string;
}

export const useReportStore = defineStore('report', {
  state: () => ({
    reports: [] as Report[],
    loading: false,
    error: null,
    reportUrl: null as string | null,
  }),
  actions: {
    async fetchReports() {
      this.loading = true;
      try {
        const response = await reportService.getAllReports();
        // Ensure response.data is an array, or default to empty array
        this.reports = Array.isArray(response.data) ? response.data : [];
      } catch (error: any) {
        this.error = error.message;
        this.reports = []; // Ensure it's an array even on error
      } finally {
        this.loading = false;
      }
    },
    async uploadReport(formData: FormData) {
      this.loading = true;
      try {
        const response = await reportService.uploadReport(formData);
        if (Array.isArray(this.reports)) {
          this.reports.push(response.data);
        } else {
          // If for some reason this.reports is not an array, reinitialize it
          this.reports = [response.data];
        }
      } catch (error: any) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    async updateReport(id: string, formData: FormData) {
      this.loading = true;
      try {
        const response = await reportService.updateReport(id, formData);
        const index = this.reports.findIndex(report => report.id === id);
        if (index !== -1) {
          this.reports[index] = response.data;
        }
      } catch (error: any) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    async deleteReport(id: string) {
      this.loading = true;
      try {
        await reportService.deleteReport(id);
        this.reports = this.reports.filter(report => report.id !== id);
      } catch (error: any) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },
    async executeReport(data: { report_id: string; data_source_id?: string; format: string; parameters?: object; json_data?: object }) {
      this.loading = true;
      this.reportUrl = null; // Clear previous URL
      try {
        const response = await reportService.executeReport(data);
        const blob = new Blob([response.data], { type: response.headers['content-type'] });
        this.reportUrl = window.URL.createObjectURL(blob);

        // No need to create and click link here, just provide the URL
        // The component will handle the download link

      } catch (error: any) {
        this.error = error.message;
        this.reportUrl = null;
      } finally {
        this.loading = false;
      }
    },
  },
});
