import { api } from "./api.js";

$(document).ready(function () {
  
    document
    .getElementById('login-form')
    .addEventListener('submit', async e => {
      e.preventDefault(); 
      
      const email      = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      try {
        const { access_token, refresh_token } = await api.login(email, password);
        localStorage.setItem('access_token', access_token);
        console.log(atob(localStorage.getItem("access_token").split(".")[1]));
        localStorage.setItem('refresh_token', refresh_token);
        window.location.href = '/dashboard';
      } catch (err) {
        const mensaje = err.response?.data?.error || err.message || 'Error desconocido';
        alert(`Login fallido: ${mensaje}`);
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
