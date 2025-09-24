import { ref } from "vue";
import { api } from "./api.js";

export default {
  template: `
    <div>
      <h2>Reportes</h2>
      <button @click="download('pdf')">Descargar PDF</button>
      <button @click="download('csv')">Descargar CSV</button>
    </div>
  `,
  setup() {
    const download = async type => {
      try {
        const filters = {}; // puedes agregar filtros si lo deseas
        const res =
          type === "pdf"
            ? await api.reportes.downloadPdf(filters)
            : await api.reportes.downloadCsv(filters);

        const blob = new Blob([res.data]);
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `reporte.${type}`;
        a.click();
      } catch (err) {
        console.error("Error al descargar reporte:", err);
      }
    };

    return { download };
  }
};


