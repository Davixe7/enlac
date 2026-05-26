<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DonationReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $data;
    public function __construct($data) { $this->data = $data; }

    public function collection() { return $this->data; }

    public function headings(): array {
        return ['Fecha', 'Donante / Padrino', 'Empresa', 'Concepto', 'Método', 'Monto', 'Moneda'];
    }

    public function map($row): array {
        $nombre = $row->donor ? "{$row->donor->first_name} {$row->donor->last_name} {$row->donor->second_last_name}" :
                 ($row->sponsor ? "{$row->sponsor->name} {$row->sponsor->last_name} {$row->sponsor->second_last_name}" : 'N/A');

        return [
            $row->payment_date,
            $nombre,
            $row->sponsor ? $row->sponsor->company_name : 'N/A',
            $row->concept,
            $row->payment_method,
            $row->amount,
            $row->currency
        ];
    }

    public function styles(Worksheet $sheet) {
        return [1 => ['font' => ['bold' => true]]];
    }
}
