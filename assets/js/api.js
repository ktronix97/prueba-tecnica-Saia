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
    console.log("Enviando token:", token)
  }
  return config
})

// 3) Interceptor de respuesta que maneja expiración y refresh automático  
http.interceptors.response.use(
  response => response,
  async error => {
    const originalRequest = error.config

    // Evita interceptar la propia petición de refresh
    if (originalRequest.url.includes("/auth/refresh-token")) {
      return Promise.reject(error)
    }

    // Solo reintenta una vez si el token expiró
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      const refresh = localStorage.getItem("refresh_token")
      if (!refresh) {
        localStorage.clear()
        window.location.href = "/login"
        return Promise.reject(error)
      }

      try {
        const { data } = await http.post("/auth/refresh-token", {
          refresh_token: refresh
        })

        //  Guarda los nuevos tokens
        localStorage.setItem("access_token", data.access_token)
        localStorage.setItem("refresh_token", data.refresh_token)

        //  Reintenta la petición original con el nuevo token
        originalRequest.headers.Authorization = `Bearer ${data.access_token}`
        return http(originalRequest)
      } catch (e) {
        localStorage.clear()
        window.location.href = "/login"
        return Promise.reject(e)
      }
    }

    return Promise.reject(error)
  }
)

// 4) Exportamos nuestro objeto API usando el instance http
export const api = {
  // Auth
  login: (email, password) =>
    http.post("/auth/login", { email, password }).then(res => res.data),

  register: data =>
    http.post("/auth/registrar", data).then(res => res.data),

  // Tareas
  tareas: {
    list: () => http.get("/tareas").then(res => console.log(res)),
    create: tarea => http.post("/tareas", tarea),
    update: (id, tarea) => http.put(`/tareas/${id}`, tarea),
    delete: id => http.delete(`/tareas/${id}`),
  },

  // Reportes
  reportes: {
    generate: filters => http.post("/reportes", filters),
    downloadPdf: filters =>
      http.post("/reportes/pdf", filters, { responseType: "blob" }),
    downloadCsv: filters =>
      http.post("/reportes/csv", filters, { responseType: "blob" }),
  },

  // Usuarios
  usuarios: {
    list: () => http.get("/usuarios"),
    create: user => http.post("/usuarios", user),
    update: (id, user) => http.put(`/usuarios/${id}`, user),
    delete: id => http.delete(`/usuarios/${id}`),
  },

  // Helper para obtener info del usuario del JWT
  getCurrentUser: () => {
    const token = localStorage.getItem("access_token")
    if (!token || !token.includes(".")) return null

    const parts = token.split(".")
    if (parts.length !== 3) return null

    try {
      const payload = JSON.parse(atob(parts[1]))
      return payload
    } catch (e) {
      console.error("Token inválido o corrupto:", e)
      return null
    }
  },
}
