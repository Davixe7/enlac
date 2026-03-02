<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RidesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['candidate.locationDetail'])->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Candidato / Beneficiario',
            'Ubicación',
            'Hora Inicio',
            'Hora Fin',
            'Hora Salida',
            'Hora Retorno',
            'Tipo de Servicio',
            'Observaciones'
        ];
    }

    public function map($ride): array
    {
        return [
            $ride->date,
            $ride->candidate->name ?? 'N/A',
            $ride->candidate->locationDetail->name ?? 'N/A', // Ajusta según tu campo de ubicación
            $ride->start_time,
            $ride->end_time,
            $ride->departure_time,
            $ride->return_time,
            strtoupper($ride->type), // 'equine' o 'rubio' en mayúsculas
            $ride->comments,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Ponemos la primera fila (encabezados) en negrita
            1 => ['font' => ['bold' => true]],
        ];
    }
}
