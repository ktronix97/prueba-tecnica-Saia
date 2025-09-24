import { ref, onMounted } from "vue";
import { api } from "./api.js";

export default {
  template: `
    <div>
      <h2>Tareas</h2>

      <button @click="addDemo">Agregar tarea demo</button>

      <ul v-if="tasks.length">
        <li v-for="t in tasks" :key="t.id">
          {{ t.titulo }} - {{ t.estado }}
          <button @click="deleteTask(t.id)">Eliminar</button>
        </li>
      </ul>

      <p v-else>No hay tareas registradas.</p>
    </div>
  `,

  setup() {
    const tasks = ref([]);

    const fetchTasks = async () => {
      try {
        const res = await api.tareas.list();
        // Maneja tanto { data: [...] } como directamente [...]
        tasks.value = Array.isArray(res?.data) ? res.data : res ?? [];
      } catch (err) {
        console.error("Error al cargar tareas:", err);
        tasks.value = [];
      }
    };

    const addDemo = async () => {
      try {
        await api.tareas.create({
          titulo: "Demo",
          estado: "pendiente"
        });
        fetchTasks(); // recarga después de crear
      } catch (err) {
        console.error("Error al crear tarea:", err);
      }
    };

    const deleteTask = async id => {
      try {
        await api.tareas.delete(id);
        fetchTasks(); // recarga después de eliminar
      } catch (err) {
        console.error("Error al eliminar tarea:", err);
      }
    };

    onMounted(fetchTasks);

    return {
      tasks,
      addDemo,
      deleteTask
    };
  }
};


