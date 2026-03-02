<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class BeneficiaryIndividualExport implements WithEvents, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $d = $this->data;
                $c = $d['candidate'];

                // --- ENCABEZADO ---
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'REPORTE INDIVIDUAL DEL BENEFICIARIO');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

                $full_name = mb_strtoupper(($c['first_name'] ?? '') . ' ' . ($c['middle_name'] ?? '') . ' ' . ($c['last_name'] ?? ''));
                $sheet->setCellValue('A2', 'BENEFICIARIO: ' . $full_name);
                $sheet->setCellValue('A3', 'PERIODO: ' . ($d['periodLabel'] ?? 'N/A') . ' ' . now()->year);
                $sheet->setCellValue('E2', 'PROGRAMA: ' . ($c['program']['name'] ?? 'N/A'));
                $sheet->setCellValue('E3', 'RESPONSABLE: ' . ($c['enlac_responsible']['name'] ?? 'N/A'));

                // --- TABLA 1: RESUMEN (Izquierda) ---
                $sheet->setCellValue('A5', 'RESUMEN GENERAL');
                $sheet->fromArray([
                    ['Concepto', 'Total'],
                    ['Días Hábiles', $d['daysCount'] ?? 0],
                    ['Asistencias', $d['attendances']['present'] ?? 0],
                    ['Faltas Just.', $d['attendances']['justified'] ?? 0],
                    ['Faltas Injust.', $d['attendances']['unjustified'] ?? 0],
                    ['Incidencias', $d['issues'] ?? 0],
                ], null, 'A6');

                // --- TABLA 2: TRANSPORTES (Derecha) ---
                $sheet->setCellValue('D5', 'RESUMEN DE TRASLADOS');
                $sheet->fromArray([
                    ['Tipo', 'Viajes'],
                    ['Traslados de Cuauhtémoc - Rubio', $d['rides']['rubio']['total'] ?? 0],
                    ['Traslados a Equinoterapia', $d['rides']['equine']['total'] ?? 0],
                ], null, 'D6');

                // --- TABLA 3: DESGLOSE DE SESIONES Y CITAS (A mitad del reporte) ---
                $sheet->setCellValue('A13', 'DETALLE DE SESIONES, CLASES O CONSULTAS POR MES');
                $sheet->getStyle('A13')->getFont()->setBold(true);

                // Encabezados de meses
                $monthHeaders = ['Categoría/Mes', 'Mes 1', 'Mes 2', 'Mes 3', 'Mes 4', 'Mes 5', 'Mes 6', 'TOTAL'];
                $sheet->fromArray($monthHeaders, null, 'A14');

                $currentRow = 15;

                // 3.1 Cargar Áreas (areaAttendances)
                if(!empty($d['areaAttendances'])){
                    foreach($d['areaAttendances'] as $areaId => $area){
                        $sheet->setCellValue('A' . $currentRow, "Área ID: " . $areaId);
                        for($m=1; $m<=6; $m++) {
                            $sheet->setCellValueByColumnAndRow($m + 1, $currentRow, $area["m$m"] ?? 0);
                        }
                        $sheet->setCellValue('H' . $currentRow, $area['total'] ?? 0);
                        $currentRow++;
                    }
                }

                // 3.2 Cargar Citas (appointments)
                if(!empty($d['appointments'])){
                    foreach($d['appointments'] as $typeId => $appt){
                        $sheet->setCellValue('A' . $currentRow, "Cita Tipo ID: " . $typeId);
                        for($m=1; $m<=6; $m++) {
                            $sheet->setCellValueByColumnAndRow($m + 1, $currentRow, $appt["m$m"] ?? 0);
                        }
                        $sheet->setCellValue('H' . $currentRow, $appt['total'] ?? 0);
                        $currentRow++;
                    }
                }

                // 3.3 Fila de Totales (La lógica de totalsRow de tu controlador)
                $sheet->setCellValue('A' . $currentRow, 'TOTAL SESIONES');
                $sheet->getStyle('A' . $currentRow)->getFont()->setBold(true);
                if(!empty($d['totalsRow'])){
                    for($m=1; $m<=6; $m++) {
                        $sheet->setCellValueByColumnAndRow($m + 1, $currentRow, $d['totalsRow']["m$m"] ?? 0);
                    }
                    $sheet->setCellValue('H' . $currentRow, array_sum($d['totalsRow']));
                }

                // --- TABLA 4: SEMÁFORO CONDUCTA (Al final) ---
                $rowScores = $currentRow + 2;
                $sheet->setCellValue('A' . $rowScores, 'RESUMEN DE RESULTADOS');
                $sheet->getStyle('A' . $rowScores)->getFont()->setBold(true);
                $rowScores++;
                $sheet->fromArray(['Categoría', 'Verde (+)', 'Amarillo (!)', 'Rojo (-)'], null, 'A' . $rowScores);

                $sRow = $rowScores + 1;
                if(isset($d['scores']) && is_array($d['scores'])){
                    foreach ($d['scores'] as $catId => $scores) {
                        if ($catId === 'totals') continue;
                        $sheet->setCellValue('A' . $sRow, "Área ID: " . $catId);
                        $sheet->setCellValue('B' . $sRow, $scores['total']['positive'] ?? 0);
                        $sheet->setCellValue('C' . $sRow, $scores['total']['warning'] ?? 0);
                        $sheet->setCellValue('D' . $sRow, $scores['total']['negative'] ?? 0);
                        $sRow++;
                    }
                }

                // --- ESTILOS ---
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']]
                ];
                $sheet->getStyle('A6:B6')->applyFromArray($headerStyle);
                $sheet->getStyle('D6:E6')->applyFromArray($headerStyle);
                $sheet->getStyle('A14:H14')->applyFromArray($headerStyle); // Header Sesiones
                $sheet->getStyle('A' . $rowScores . ':D' . $rowScores)->applyFromArray($headerStyle); // Header Conducta

                // Bordes para la tabla de sesiones
                $sheet->getStyle('A14:H' . $currentRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
