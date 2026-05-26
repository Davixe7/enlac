<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DonorVisitReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function collection() {
        return $this->data;
    }

    public function headings(): array {
        return [
            'Fecha Visita',
            'Donante',
            'Responsable',
            'Motivo',
            'Resultado',
            'Último Donativo Radiomaratón'
        ];
    }

    public function map($row): array {
        return [
            $row->visit_date,
            $row->donor ? "{$row->donor->first_name} {$row->donor->last_name} {$row->donor->second_last_name}" : 'N/A',
            $row->responsible ? "{$row->responsible->name} {$row->responsible->last_name} {$row->responsible->second_last_name}" : 'N/A',
            $row->reason ?? '',
            $row->result ?? '',
            $row->last_radiomaraton_amount ?? 0
        ];
    }

    // Aplica estilos (negritas y bordes a los encabezados)
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo para la primera fila (encabezados)
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
