import axios from 'axios'

const BASE_URL = import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api'

export const apiClient = axios.create({
  baseURL: BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Injeta o Bearer token e corrige Content-Type para FormData
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem('potenza_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }

  // Em axios v1.x, setContentType(undefined) é no-op — o default
  // 'application/json' vaza para requisições multipart, fazendo o
  // backend receber Content-Type errado e rejeitar o upload com 422.
  // Removemos explicitamente para o browser colocar o boundary correto.
  if (config.data instanceof FormData) {
    config.headers.delete('Content-Type')
  }

  return config
})

// Trata erro 401: limpa token e redireciona para login
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('potenza_token')
      localStorage.removeItem('potenza_user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  },
)
