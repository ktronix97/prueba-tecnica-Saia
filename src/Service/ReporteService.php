<?php
// src/Service/ReporteService.php

namespace App\Service;

use App\Entity\Reporte;
use App\Repository\ReporteRepository;
use App\Repository\TareaRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReporteService
{
    public function __construct(
        private TareaRepository     $tareaRepo,
        private ReporteRepository   $reporteRepo,
        private EntityManagerInterface $em,
        private string              $projectDir      // inyectar %kernel.project_dir%
    ) {}

    /**
     * Crea un reporte por cada tarea generada HOY y lo persiste.
     * @return Reporte[] Array de entidades Reporte creadas
     */
    public function generarReporteDiario(): array
    {
        $hoy   = new DateTimeImmutable('today');
        $mañana= $hoy->modify('+1 day');

        $tareas = $this->tareaRepo->findByFilters([
            'fecha_inicio' => $hoy->format('Y-m-d'),
            'fecha_fin'    => $mañana->format('Y-m-d'),
        ]);

        $reportes = [];
        foreach ($tareas as $tarea) {
            $contenido = $this->formatContenido($tarea);
            $reporte = new Reporte();
            $reporte->setTarea($tarea)
                    ->setContenido($contenido);
            $this->em->persist($reporte);
            $reportes[] = $reporte;
        }

        $this->em->flush();
        return $reportes;
    }

    /**
     * Genera un reporte en memoria según filtros.
     * No persiste entidades Reporte.
     *
     * Filtros admitidos: estado, prioridad, usuario, rango de fechas.
     * @param array $filtros
     * @return array Cada elemento: ['tarea'=>Tarea, 'contenido'=>string]
     */
    public function generarReportePersonalizado(array $filtros): array
    {
        $tareas = $this->tareaRepo->findByFilters($filtros);
        $resultado = [];

        foreach ($tareas as $tarea) {
            $resultado[] = [
                'tarea'     => $tarea,
                'contenido' => $this->formatContenido($tarea),
            ];
        }

        return $resultado;
    }

    /**
     * Exporta un reporte persistido (por id) a PDF o CSV.
     * Devuelve la ruta relativa al directorio public/.
     *
     * @param int    $idReporte
     * @param string $formato 'pdf' o 'csv'
     * @return string Ruta del archivo generado
     */
    public function exportar(int $idReporte, string $formato): string
    {
        $reporte = $this->reporteRepo->find($idReporte);
        if (!$reporte) {
            throw new \InvalidArgumentException('Reporte no encontrado');
        }

        $dir = $this->projectDir . '/public/reports';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $basename = sprintf('reporte_%d_%s', $idReporte, date('YmdHis'));

        if ($formato === 'csv') {
            $filePath = "$dir/{$basename}.csv";
            $handle = fopen($filePath, 'w');
            // Cabecera
            fputcsv($handle, ['ID Tarea', 'Título', 'Fecha Reporte', 'Contenido']);
            // Fila
            fputcsv($handle, [
                $reporte->getTarea()->getId(),
                $reporte->getTarea()->getTitulo(),
                $reporte->getFecha()->format('Y-m-d H:i:s'),
                $reporte->getContenido(),
            ]);
            fclose($handle);
        } else {
            // PDF
            $filePath = "$dir/{$basename}.pdf";

            $options = new Options();
            $options->set('isRemoteEnabled', false);
            $dompdf = new Dompdf($options);

            $html = sprintf(
                '<h1>Reporte #%d</h1>
                <p><strong>Tarea:</strong> %s</p>
                <p><strong>Fecha:</strong> %s</p>
                <p><strong>Contenido:</strong></p>
                <div>%s</div>',
                $reporte->getId(),
                htmlspecialchars($reporte->getTarea()->getTitulo(), ENT_QUOTES),
                $reporte->getFecha()->format('Y-m-d H:i:s'),
                nl2br(htmlspecialchars($reporte->getContenido(), ENT_QUOTES))
            );

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($filePath, $dompdf->output());
        }

        // Retornamos ruta pública
        return '/reports/' . basename($filePath);
    }

    /**
     * Formatea los datos de una tarea en texto legible para el contenido del reporte.
     */
    private function formatContenido($tarea): string
    {
        return sprintf(
            "Tarea: %s\nEstado: %s\nPrioridad: %s\nAsignado a: %s\nCreada: %s\nVence: %s\n",
            $tarea->getTitulo(),
            $tarea->getEstado(),
            $tarea->getPrioridad(),
            $tarea->getAsignadoA()?->getEmail() ?? '—',
            $tarea->getFechaCreacion()->format('Y-m-d'),
            $tarea->getFechaVencimiento()?->format('Y-m-d') ?? '—'
        );
    }
}
