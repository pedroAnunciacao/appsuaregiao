// api.js
import axios from "axios"

// ✅ Detecta se está rodando localmente
const isLocalhost = window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1"

// ✅ Configuração base da API
const api = axios.create({
  baseURL: isLocalhost
    ? "http://127.0.0.1:8000/api" // Ambiente local
    : "https://f7afceb5eca7.ngrok-free.app/api", // Produção
  withCredentials: true, // Necessário se o backend usar cookies (ex: Laravel Sanctum)
})

// ✅ Interceptor para adicionar o token JWT automaticamente (se existir)
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("token")
    const userId = localStorage.getItem("userId")
    const selectedRegionId = localStorage.getItem("selectedRegionId")

    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    if (userId) {
      config.headers["X-Content"] = userId
    }

    if (selectedRegionId) {
      config.headers["X-region_selected"] = selectedRegionId
    }

    return config
  },
  (error) => Promise.reject(error),
)

export default api
