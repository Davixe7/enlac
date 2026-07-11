<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Carbon\Carbon;

class RidesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $query, $range;
    // Propiedad para llevar el control del No. Progresivo fila por fila
    protected $rowNumber = 0;

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

                // Expandimos la combinación hasta la columna I para incluir la discapacidad
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'BITACORA MENSUAL DE SERVICIOS DE TRASLADO');
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'FECHA DE CONSULTA: ' . $this->range);

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
                    // Cargamos los contactos para poder buscar al tutor legal directamente
                    ->with(['locationDetail', 'contacts']);
            }])
            ->get();

        // Agrupamos los traslados por candidato y por fecha para consolidar los traslados múltiples
        $groupedRides = $rides->groupBy(function($item) {
            return $item->candidate_id . '-' . $item->date;
        });

        return $groupedRides->map(function ($group) {
            // Tomamos el primer registro del grupo para extraer los datos base comunes
            $firstRide = $group->first();
            $candidate = $firstRide->candidate;

            // Buscamos el contacto que sea Tutor Legal (legal_guardian == true)
            $legalGuardian = $candidate->contacts->first(function ($contact) {
                return (bool) $contact->legal_guardian === true;
            });

            // Si tiene tutor legal usamos su WhatsApp o Teléfono, de lo contrario 'N/A'
            $guardianPhone = 'N/A';
            if ($legalGuardian) {
                // Prioriza whatsapp si existe, si no busca home_phone
                $guardianPhone = $legalGuardian->whatsapp ?: ($legalGuardian->home_phone ?: 'N/A');
            }

            // Calculamos la cantidad de traslados sumando las idas y vueltas válidas del grupo
            $totalTraslados = 0;
            foreach ($group as $ride) {
                if ($ride->departure_time) $totalTraslados++;
                if ($ride->return_time) $totalTraslados++;
            }

            $formattedDate = $firstRide->date ? Carbon::parse($firstRide->date)->format('d/m/Y') : 'N/A';

            return [
                'fecha'        => $formattedDate,
                'nombre'       => $candidate->full_name ?? 'N/A',
                'curp'         => $candidate->locationDetail->curp ?? 'N/A',
                'localidad'    => $candidate->locationDetail->locality_name ?? 'N/A',
                'destino'      => 'ENLAC - DOMICILIO',
                'traslados'    => $totalTraslados,
                'celular'      => $guardianPhone,
                'discapacidad' => $candidate->diagnosis ?? 'N/A'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No. Progresivo',
            'Fecha',
            'Nombre completo del beneficiario',
            'CURP del beneficiario',
            'Localidad o Domicilio',
            'Destino',
            'Traslados',
            'Celular del beneficiario y/o de su familiar',
            'Nombre discapacidad del beneficiario'
        ];
    }

    public function map($ride): array
    {
        // Incrementamos el contador progresivo en cada fila mapeada
        $this->rowNumber++;

        return [
            $this->rowNumber, // Añade el número secuencial (1, 2, 3...)
            $ride['fecha'],
            $ride['nombre'],
            $ride['curp'],
            $ride['localidad'],
            $ride['destino'],
            $ride['traslados'],
            $ride['celular'],
            $ride['discapacidad'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // 1. Pone en negrita la fila 3 (Encabezados)
            3 => ['font' => ['bold' => true]],

            // 2. Centra absolutamente TODAS las columnas desde la A hasta la I por completo
            'A:I' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ]
            ],
        ];
    }
}
