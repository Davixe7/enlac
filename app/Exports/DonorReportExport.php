<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DonorReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
            'Nombre del Donante',
            'Empresa',
            'Puesto',
            'Teléfono Celular',
            'Fecha de Cumpleaños',
            'Sector',
            'Estatus'
        ];
    }

    public function map($row): array {
        $fullName = $row->full_name ?? trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '') . ' ' . ($row->second_last_name ?? ''));

        $birthDate = $row->birth_date
            ? \Carbon\Carbon::parse($row->birth_date)->format('d/m')
            : '';

        return [
            $fullName ?: 'Donante Sin Nombre',
            $row->company_name ?? '',
            $row->job_title ?? '',
            $row->cellphone ?? '',
            $birthDate, // <--- Columna con la fecha limpia
            $row->sector ?? '',
            $row->is_active ? 'Activo' : 'Inactivo'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
