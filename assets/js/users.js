import { ref, onMounted } from "vue";
import { api } from "./api.js";

export default {
  template: `
    <div>
      <h2>Usuarios</h2>

      <ul v-if="users.length">
        <li v-for="u in users" :key="u.id">
          {{ u.email }} - {{ u.roles?.join(", ") }}
        </li>
      </ul>

      <p v-else>No hay usuarios registrados o no tienes permisos.</p>
    </div>
  `,
  setup() {
    const users = ref([]);

    const fetchUsers = async () => {
      try {
        const res = await api.usuarios.list();
        users.value = Array.isArray(res?.data) ? res.data : res ?? [];
      } catch (err) {
        console.error("Error al cargar usuarios:", err);
        users.value = [];
      }
    };

    onMounted(fetchUsers);
    return { users };
  }
};


