import { api } from "./api.js";
import "./dashboard.js";
 
document.addEventListener("DOMContentLoaded", () => {
  const user = api.getCurrentUser();

  if (user) {
    // Mostrar elementos de sesión activa
    const navLogin = document.getElementById("nav-login");
    const navDashboard = document.getElementById("nav-dashboard");
    const navLogout = document.getElementById("nav-logout");

    if (navLogin) navLogin.classList.add("d-none");
    if (navDashboard) navDashboard.classList.remove("d-none");
    if (navLogout) navLogout.classList.remove("d-none");

    // Mostrar email en dashboard si existe el contenedor
    const emailEl = document.getElementById("user-email");
    if (emailEl) emailEl.textContent = user.email;

    // Mostrar sección admin si el rol lo permite
    if (user.roles?.includes("ROLE_ADMIN")) {
      const adminSection = document.getElementById("admin-section");
      if (adminSection) adminSection.classList.remove("d-none");
    }
  } else {
    // Usuario no autenticado
    const navLogin = document.getElementById("nav-login");
    const navDashboard = document.getElementById("nav-dashboard");
    const navLogout = document.getElementById("nav-logout");

    if (navLogin) navLogin.classList.remove("d-none");
    if (navDashboard) navDashboard.classList.add("d-none");
    if (navLogout) navLogout.classList.add("d-none");

    // Si estás en una vista protegida, redirige
    const emailEl = document.getElementById("user-email");
    if (emailEl) window.location.href = "/login";
  }

  // Logout funcional
  const navLogout = document.getElementById("nav-logout");
  if (navLogout) {
    navLogout.addEventListener("click", e => {
      e.preventDefault();
      localStorage.clear();
      window.location.href = "/login";
    });
  }
});


