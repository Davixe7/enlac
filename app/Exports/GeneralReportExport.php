<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class GeneralReportExport implements WithEvents, ShouldAutoSize
{
    protected $data;
    protected $start;
    protected $end;

    public function __construct($data, $start, $end)
    {
        $this->data = $data;
        $this->start = $start;
        $this->end = $end;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $d = $this->data;

                // --- AJUSTE MANUAL DE ANCHO DE COLUMNAS ---
                // Forzamos un ancho mayor para las columnas de la derecha (Padrinos y Tesorería)
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(5); // Espaciador
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(25);

                // --- ENCABEZADO PRINCIPAL ---
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', 'REPORTE GENERAL DE INDICADORES');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

                $sheet->setCellValue('A2', 'PERIODO: ' . Carbon::parse($this->start)->format('d/m/Y') . ' al ' . Carbon::parse($this->end)->format('d/m/Y'));
                $sheet->getStyle('A2')->getFont()->setItalic(true);

                $styleHeader = [
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4B5563']]
                ];

                // --- SECCIÓN 1: CANDIDATOS (Columna A-B) ---
                $sheet->setCellValue('A5', 'INDICADORES DE CANDIDATOS');
                $sheet->getStyle('A5')->getFont()->setBold(true);
                $sheet->fromArray(['Concepto', 'Cantidad'], null, 'A6');
                $sheet->getStyle('A6:B6')->applyFromArray($styleHeader);
                $sheet->fromArray([
                    ['Evaluaciones Realizadas', $d['candidates']['evaluations']],
                    ['Candidatos Aceptados', $d['candidates']['accepted']],
                    ['Candidatos Rechazados', $d['candidates']['rejected']],
                ], null, 'A7');

                // --- SECCIÓN 2: BENEFICIARIOS (Columna A-B) ---
                $sheet->setCellValue('A11', 'INDICADORES DE BENEFICIARIOS');
                $sheet->getStyle('A11')->getFont()->setBold(true);
                $sheet->fromArray(['Concepto', 'Cantidad / Valor'], null, 'A12');
                $sheet->getStyle('A12:B12')->applyFromArray($styleHeader);
                $sheet->fromArray([
                    ['Beneficiarios Activos', $d['beneficiaries']['active']],
                    ['Beneficiarios Programados', $d['beneficiaries']['programed']],
                    ['Promedio de Asistencia', $d['beneficiaries']['attendance'] . '%'],
                ], null, 'A13');

                // --- SECCIÓN 3: PADRINOS (Columna D-E) ---
                $sheet->setCellValue('D5', 'INDICADORES DE PADRINOS');
                $sheet->getStyle('D5')->getFont()->setBold(true);
                $sheet->fromArray(['Concepto', 'Total'], null, 'D6');
                $sheet->getStyle('D6:E6')->applyFromArray($styleHeader);
                $sheet->fromArray([
                    ['Total de Padrinos', $d['sponsors']['total']],
                    ['Beneficiarios con Padrino', $d['sponsors']['beneficiaries']],
                    ['Aportaciones Enlac', $d['sponsors']['enlac']],
                ], null, 'D7');

                // --- SECCIÓN 4: TESORERÍA (Columna D-E) ---
                $sheet->setCellValue('D11', 'INDICADORES DE TESORERÍA (INGRESOS)');
                $sheet->getStyle('D11')->getFont()->setBold(true);
                $sheet->fromArray(['Origen del Pago', 'Monto Acumulado'], null, 'D12');
                $sheet->getStyle('D12:E12')->applyFromArray($styleHeader);

                $totalIngresos = $d['payments']['parent'] + $d['payments']['sponsor'];

                $sheet->fromArray([
                    ['Pagos de Padres de Familia', $d['payments']['parent']],
                    ['Pagos de Padrinos', $d['payments']['sponsor']],
                    ['TOTAL INGRESOS', $totalIngresos],
                ], null, 'D13');

                // Aplicar formato de moneda a los montos de tesorería (Columna E)
                $sheet->getStyle('E13:E15')->getNumberFormat()->setFormatCode('$#,##0.00');
                $sheet->getStyle('D15:E15')->getFont()->setBold(true);

                // --- SECCIÓN 5: TRASLADOS (Columna A-B) ---
                $sheet->setCellValue('A17', 'INDICADORES DE TRASLADOS');
                $sheet->getStyle('A17')->getFont()->setBold(true);
                $sheet->fromArray(['Servicio', 'Total de Recorridos (Ida/Vuelta)'], null, 'A18');
                $sheet->getStyle('A18:B18')->applyFromArray($styleHeader);
                $sheet->fromArray([
                    ['Traslados Equinoterapia', $d['rides']['equine']],
                    ['Traslados Ruta Rubio', $d['rides']['rubio']],
                ], null, 'A19');

                // --- BORDES PARA TODAS LAS TABLAS ---
                $sheet->getStyle('A6:B9')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('A12:B15')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('D6:E9')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('D12:E15')->getBorders()->getAllBorders()->setBorderStyle('thin');
                $sheet->getStyle('A18:B21')->getBorders()->getAllBorders()->setBorderStyle('thin');
            },
        ];
    }
}
