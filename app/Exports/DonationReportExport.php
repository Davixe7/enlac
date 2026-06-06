<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class DonationReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $data;
    public function __construct($data) { $this->data = $data; }

    public function collection() { return $this->data; }

    public function headings(): array {
        return [
            'No. Folio',          // Columna 1 (A)
            'Fecha',              // Columna 2 (B)
            'Donante / Padrino',  // Columna 3 (C)
            'Empresa',            // Columna 4 (D)
            'Tipo de Actividad',  // Columna 5 (E)
            'Actividad Específica',// Columna 6 (F)
            'Concepto',           // Columna 7 (G)
            'Método',             // Columna 8 (H)
            'Monto',              // Columna 9 (I) -> Formateada como moneda
            'Moneda'              // Columna 10 (J)
        ];
    }

    public function map($row): array {
        $nombre = $row->donor ? trim("{$row->donor->first_name} {$row->donor->last_name} {$row->donor->second_last_name}") :
                 ($row->sponsor ? trim("{$row->sponsor->name} {$row->sponsor->last_name} {$row->sponsor->second_last_name}") : 'N/A');

        return [
            $row->folio_number,
            \Carbon\Carbon::parse($row->payment_date)->format('d/m/Y'),
            $nombre ?: 'N/A',
            $row->sponsor && $row->sponsor->company_name ? $row->sponsor->company_name : 'N/A',
            $row->activity_type ?? 'N/A',
            $row->procurationActivity ? $row->procurationActivity->name : 'N/A',
            $row->concept,
            $row->payment_method,
            (float) $row->amount, // Lo forzamos a numérico para que Excel lo reconozca
            $row->currency
        ];
    }

    /**
     * Aplica el formato numérico de moneda a la columna I (Monto)
     */
    public function columnFormats(): array {
        return [
            'I' => '"$ " #,##0.00'
        ];
    }

    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true]]];
    }
}
