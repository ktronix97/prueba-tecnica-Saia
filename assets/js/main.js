import axios from "axios";
import { createApp } from "vue";

// Configuración global de Axios
axios.defaults.baseURL = "/api";
axios.interceptors.request.use(config => {
    const token = localStorage.getItem("access_token");
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

// Interceptor de refresh token
axios.interceptors.response.use(
    response => response,
    async error => {
        if (error.response?.status === 401) {
            const refresh = localStorage.getItem("refresh_token");
            if (refresh) {
                try {
                    const { data } = await axios.post("/auth/refresh", { refresh_token: refresh });
                    localStorage.setItem("access_token", data.access_token);
                    error.config.headers.Authorization = `Bearer ${data.access_token}`;
                    return axios(error.config);
                } catch (e) {
                    localStorage.clear();
                    window.location.href = "/login";
                }
            }
        }
        return Promise.reject(error);
    }
);

// Montaje condicional de apps Vue
import Tasks from "./tasks.js";
import Reports from "./reports.js";
import Users from "./users.js";

if (document.querySelector("#app-tasks")) {
    createApp(Tasks).mount("#app-tasks");
}
if (document.querySelector("#app-reports")) {
    createApp(Reports).mount("#app-reports");
}
if (document.querySelector("#app-users")) {
    createApp(Users).mount("#app-users");
}
