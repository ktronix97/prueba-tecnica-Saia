import { ref, onMounted } from "vue";
import { api } from "./api.js";

export default {
setup() {
    const users = ref([]);

    const fetchUsers = async () => {
        const { data } = await api.users.list();
        users.value = data;
    };

    const deleteUser = async (id) => {
        await api.users.delete(id);
        fetchUsers();
    };

    onMounted(fetchUsers);

    return { users, deleteUser };
    },
template: `
    <div>
        <h2>Usuarios</h2>
        <ul>
            <li v-for="u in users" :key="u.id">
                {{ u.email }} ({{ u.rol }})
                <button @click="deleteUser(u.id)">Eliminar</button>
            </li>
        </ul>
    </div>
`
};
