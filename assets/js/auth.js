import { api } from "./api.js";

$(document).ready(function () {
    // Login
    document
    .getElementById('login-form')
    .addEventListener('submit', async e => {
      e.preventDefault(); // ⬅️ evita el GET nativo
      
      const email      = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      try {
        const { data } = await api.login(email, password);
        localStorage.setItem('access_token', data.access_token);
        window.location.href = '/dashboard';
      } catch (err) {
        alert(err.response?.data?.error || 'Credenciales inválidas');
      }
    });

    document.getElementById('register-form')?.addEventListener('submit', async e => {
      e.preventDefault();

      const payload = {
        primerNombre:    document.getElementById('primerNombre').value,
        segundoNombre:   document.getElementById('segundoNombre').value,
        primerApellido:  document.getElementById('primerApellido').value,
        segundoApellido: document.getElementById('segundoApellido').value,
        email:           document.getElementById('email').value,
        password:        document.getElementById('password').value,
        rol:             document.getElementById('rol').value,
      };

      try {
        await api.register(payload);
        alert('Registro exitoso');
        window.location.href = '/login';
      } catch {
        alert('Error en el registro');
      }
    });

  // Logout
    $("#logout").on("click", function () {
        localStorage.clear();
        window.location.href = "/login";
    });
});
