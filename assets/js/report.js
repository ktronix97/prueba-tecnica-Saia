import { ref } from "vue";
import { api } from "./api.js";

export default {
    setup() {
    const filters = ref({ estado: "", prioridad: "", usuario: "" });

    const download = async (type) => {
        const fn = type === "pdf" ? api.reports.downloadPdf : api.reports.downloadCsv;
        const { data } = await fn(filters.value);
        const blob = new Blob([data]);
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `reporte.${type}`;
        a.click();
    };

    return { filters, download };
    },
template: `
    <div>
        <h2>Reportes</h2>
        <button @click="download('pdf')">Descargar PDF</button>
        <button @click="download('csv')">Descargar CSV</button>
    </div>
`
};

