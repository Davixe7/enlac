<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExecutiveReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    protected $data;
    protected $year;

    public function __construct($data, $year)
    {
        $this->data = $data;
        $this->year = $year;
    }

    public function collection()
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            'NOMBRE DEL BENEFICIARIO',
            'ASISTENCIAS',
            'JUSTIFICADAS',
            'INJUSTIFICADAS',
            'INCIDENCIAS',
            'TRANSPORTES RUBIO',
            'TRANSPORTES EQUINO',
            'TOTAL POSITIVOS (VERDE)',
            'TOTAL ADVERTENCIAS (AMARILLO)',
            'TOTAL NEGATIVOS (ROJO)',
        ];
    }

    public function map($candidate): array
    {
        // Sumamos los scores de todos los meses para el resumen anual
        $pos = $candidate['months']->sum('positive');
        $war = $candidate['months']->sum('warning');
        $neg = $candidate['months']->sum('negative');

        return [
            mb_strtoupper($candidate['full_name']),
            $candidate['present'],
            $candidate['justified'],
            $candidate['unjustified'],
            $candidate['issues'],
            $candidate['rubio'],
            $candidate['equine'],
            $pos,
            $war,
            $neg,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937']
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'REPORTE EJECUTIVO ANUAL - RESUMEN DE GESTIÓN');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);

                $sheet->setCellValue('A2', 'AÑO FISCAL: ' . $this->year);
                $sheet->setCellValue('A3', 'FECHA DE DESCARGA: ' . now()->format('d/m/Y H:i'));

                // Bordes para la tabla
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:J$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
