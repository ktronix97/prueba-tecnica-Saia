import { api } from "./api.js";
import { createApp } from "vue";
import Tasks from "./task.js";
import Reports from "./report.js";
import Users from "./users.js";

document.addEventListener("DOMContentLoaded", () => {
  const user = api.getCurrentUser();

  if (!user) {
    console.warn("Token inválido o no presente. Redirigiendo...");
    window.location.href = "/login";
    return;
  }

  // ✅ Mostrar elementos del navbar según sesión
  document.getElementById("nav-dashboard")?.classList.remove("d-none");
  document.getElementById("nav-logout")?.classList.remove("d-none");
  document.getElementById("nav-login")?.classList.add("d-none");

  const emailElement = document.getElementById("user-email");
  if (emailElement) {
    emailElement.textContent = user.email;
  }

  if (user.roles?.includes("ROLE_ADMIN")) {
    document.getElementById("admin-section")?.classList.remove("d-none");
    //  Montar componente de usuarios solo si tiene el rol
    if (document.querySelector("#app-users")) {
      console.log("Montando usuarios...");
      createApp(Users).mount("#app-users");
    }
  }

  // Montar componente de tareas
  if (document.querySelector("#app-tasks")) {
    console.log("Montando tareas...");
    createApp(Tasks).mount("#app-tasks");
  }

  // ✅ Montar componente de reportes
  if (document.querySelector("#app-reports")) {
    console.log("Montando reportes...");
    createApp(Reports).mount("#app-reports");
  }

  // ✅ Logout seguro
  document.getElementById("nav-logout")?.addEventListener("click", () => {
    localStorage.clear();
    window.location.href = "/login";
  });
});
