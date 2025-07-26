interface DataSource {
  id: number;
  name: string;
  type: string;
  // Adicione outras propriedades da sua fonte de dados aqui, se houver
}

interface NewDataSource {
  name: string;
  type: string;
}

interface AuthError {
  message: string;
  errors?: { [key: string]: string[] };
}