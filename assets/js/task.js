import { ref, onMounted } from "vue";
import { api } from "./api.js";

export default {
    setup() {
    const tasks = ref([]);
    const loading = ref(false);

    const fetchTasks = async () => {
        loading.value = true;
        const { data } = await api.tasks.list();
        tasks.value = data;
        loading.value = false;
    };

    const addTask = async (task) => {
        await api.tasks.create(task);
        fetchTasks();
    };

    const deleteTask = async (id) => {
        await api.tasks.delete(id);
        fetchTasks();
    };

    onMounted(fetchTasks);

    return { tasks, loading, addTask, deleteTask };
    },
template: `
    <div>
        <h2>Tareas</h2>
        <ul>
            <li v-for="t in tasks" :key="t.id">
            {{ t.titulo }} - {{ t.estado }}
            <button @click="deleteTask(t.id)">Eliminar</button>
            </li>
        </ul>
    </div>
`
};
