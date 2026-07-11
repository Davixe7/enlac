<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;

class RidesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $query, $range;

    public function __construct($query, $range = '')
    {
        $this->query = $query;
        $this->range = $range;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet;

                // 1. Insertamos espacio para los títulos (esto baja todo)
                //$sheet->insertNewRowBefore(1, 3);

                // 2. Ahora escribimos los títulos en las filas nuevas
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'BITACORA MENSUAL DE SERVICIOS DE TRASLADO');
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', 'FECHA DE CONSULTA: ' . $this->range);

                // Los encabezados que define WithHeadings se escribirán automáticamente en la fila 4
                // gracias al insertNewRowBefore(1, 3)

                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
            },
        ];
    }

    public function collection()
    {
        $rides = $this->query
        ->with(['candidate' => function ($query) {
            $query
            ->fullName()
            ->with(['locationDetail', 'legalGuardian']);
        }])
        ->get();

        return $rides->flatMap(function ($ride) {
            $base = $ride->toArray();
            $candidate = $ride->candidate;

            $filas = [];

            $datosComunes = [
                'fecha'        => $ride->date,
                'nombre'       => $candidate->full_name ?? 'N/A',
                'curp'         => $candidate->locationDetail->curp ?? 'N/A',
                'localidad'    => $candidate->locationDetail->transport_address ?? 'N/A',
                'celular'      => $candidate->legalGuardian->phones ?? 'N/A',
                'discapacidad' => $candidate->diagnosis ?? 'N/A'
            ];

            if ($ride->departure_time) {
                $filas[] = array_merge($datosComunes, ['destino' => 'ENLAC', 'time' => $ride->departure_time]);
            }

            if ($ride->return_time) {
                $filas[] = array_merge($datosComunes, ['destino' => 'DOMICILIO', 'time' => $ride->return_time]);
            }

            return $filas;
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Nombre completo del beneficiario',
            'CURP del beneficiario',
            'Localidad o Domicilio',
            'Destino',
            'Celular del beneficiario y/o de su familiar',
            'Nombre discapacidad del beneficiario'
        ];
    }

    public function map($ride): array
    {
        // $ride ahora es un array asociativo gracias al flatMap
        return [
            $ride['fecha'],
            $ride['nombre'],
            $ride['curp'],
            $ride['localidad'],
            $ride['destino'], // Este es el que cambia entre filas
            $ride['celular'],
            $ride['discapacidad'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Ponemos la primera fila (encabezados) en negrita
            3 => ['font' => ['bold' => true]],
        ];
    }
}
