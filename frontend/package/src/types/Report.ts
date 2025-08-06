export interface Report {
  id: number;
  name: string;
  description: string;
  file_path: string;
  created_at: string;
  updated_at: string;
  data_source_id?: number;
}
