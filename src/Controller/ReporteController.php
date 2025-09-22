<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ReporteService;

#[Route('/api/reportes')]
final class ReporteController extends AbstractController
{
    #[Route('/diario', name: 'api_reporte_diario', methods: ['GET'])]
    public function diario(ReporteService $reporteService): JsonResponse
    {
        $data = $reporteService->generarReporteDiario();
        return $this->json($data);
    }

    #[Route('/personalizado', name: 'api_reporte_personalizado', methods: ['POST'])]
    public function personalizado(Request $request, ReporteService $reporteService): JsonResponse
    {
        $filtros = json_decode($request->getContent(), true);
        $data = $reporteService->generarReportePersonalizado($filtros);
        return $this->json($data);
    }

    #[Route('/{id}/exportar', name: 'api_reporte_exportar', methods: ['GET'])]
    public function exportar(int $id, Request $request, ReporteService $reporteService): JsonResponse
    {
        $formato = $request->query->get('formato', 'pdf');
        $archivo = $reporteService->exportar($id, $formato);

        return $this->json(['mensaje' => "Reporte exportado en formato $formato", 'archivo' => $archivo]);
    }
}

