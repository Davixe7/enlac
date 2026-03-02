<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
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

    public function collection()
    {
        return $this->data;
    }

    // La tabla de datos empezará en la fila 4 para dejar espacio arriba
    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        return [
            'ID CANDIDATO',
            'NOMBRE COMPLETO',
            'ASISTENCIAS',
            'FALTAS JUSTIFICADAS',
            'FALTAS INJUSTIFICADAS',
            'PORCENTAJE (%)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->candidate_id,
            mb_strtoupper($row->full_name),
            $row->present . ' días',
            $row->justified,
            $row->unjustified,
            $row->percentage . '%',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para los encabezados de la tabla
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2C3E50']
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Título Principal
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'REPORTE GENERAL DE ASISTENCIA');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Rango de Fechas del reporte
                $sheet->setCellValue('A2', 'PERIODO: ' . Carbon::parse($this->start)->format('d/m/Y') . ' al ' . Carbon::parse($this->end)->format('d/m/Y'));

                // Fecha de emisión
                $sheet->setCellValue('A3', 'FECHA DE EMISIÓN: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle('A2:A3')->getFont()->setItalic(true);
            },
        ];
    }
}
