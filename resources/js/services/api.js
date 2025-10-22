// api.js
import axios from 'axios';

// ✅ Detecta se está rodando localmente
const isLocalhost =
  window.location.hostname === 'localhost' ||
  window.location.hostname === '127.0.0.1';

// ✅ Configuração base da API
const api = axios.create({
  baseURL: isLocalhost
    ? 'http://127.0.0.1:8000/api'      // Ambiente local
    : 'https://appsuaregiao.com.br/api', // Produção
  withCredentials: true, // Necessário se o backend usar cookies (ex: Laravel Sanctum)
});

// ✅ Interceptor para adicionar o token JWT automaticamente (se existir)
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // Adiciona X-Content com o ID do usuário
    const userId = localStorage.getItem('id');
    if (userId) {
      config.headers['X-Content'] = userId;
    }

    // Adiciona X-region_selected se existir uma região selecionada
    const selectedRegion = localStorage.getItem('selected_region_id');
    if (selectedRegion) {
      config.headers['X-region_selected'] = selectedRegion;
    }

    return config;
  },
  (error) => Promise.reject(error)
);

export default api;
