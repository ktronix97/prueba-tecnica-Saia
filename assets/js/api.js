// src/js/api.js
import axios from "axios"

// 1) Creamos un axios instance con baseURL apuntando a /api  
const http = axios.create({
    baseURL: "/api",
    headers: { "Content-Type": "application/json" },
})

// 2) Interceptor de petición que añade el Bearer token automáticamente  
http.interceptors.request.use(config => {
    const token = localStorage.getItem("access_token")
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

// 3) Interceptor de respuesta que maneja 401 (opcional)  
http.interceptors.response.use(
    res => res,
    err => {
    if (err.response?.status === 401) {
      // por ejemplo, si expira el token redirigimos a login
      window.location.href = "/login"
    }
    return Promise.reject(err)
  }
)

// 4) Exportamos nuestro objeto API usando el instance http
export const api = {
  // Auth
  login: (email, contrasena) =>
    http.post("/auth/login", { email, contrasena }),

  register: data =>
    http.post("/auth/registrar", data),

  // Tareas
  tareas: {
    list: () =>
      http.get("/tareas"),
    create: tarea =>
      http.post("/tareas", tarea),
    update: (id, tarea) =>
      http.put(`/tareas/${id}`, tarea),
    delete: id =>
      http.delete(`/tareas/${id}`),
  },

  // Reportes
  reportes: {
    generate: filters =>
      http.post("/reportes", filters),
    downloadPdf: filters =>
      http.post("/reportes/pdf", filters, { responseType: "blob" }),
    downloadCsv: filters =>
      http.post("/reportes/csv", filters, { responseType: "blob" }),
  },

  // Usuarios
  usuarios: {
    list: () =>
      http.get("/usuarios"),
    create: user =>
      http.post("/usuarios", user),
    update: (id, user) =>
      http.put(`/usuarios/${id}`, user),
    delete: id =>
      http.delete(`/usuarios/${id}`),
  },

  // Helper para obtener info del usuario del JWT
  getCurrentUser: () => {
    const token = localStorage.getItem("access_token")
    if (!token) return null
    // parsea el payload (sin validar firma)
    const payload = JSON.parse(atob(token.split(".")[1]))
    return payload
  },
}

