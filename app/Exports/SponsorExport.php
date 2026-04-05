<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SponsorExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['payment_configs.candidate'])->get();
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Nombre Completo',
            'Empresa',
            'Cant. Beneficiarios',
            'Lista de Beneficiarios',
            '¿Es Anónimo?',
            'Contactado por',
            'Fecha de Registro',
        ];
    }

    public function map($sponsor): array
    {
        $beneficiarios = $sponsor->payment_configs->map(function($config) {
            return $config->candidate->full_name ?? 'N/A';
        })->filter()->unique()->implode(', ');

        $fullName = implode(' ', array_filter([
            $sponsor->name,
            $sponsor->last_name,
            $sponsor->second_last_name
        ]));

        return [
            str_pad($sponsor->id, 4, '0', STR_PAD_LEFT), // Folio formateado
            $fullName,
            $sponsor->company_name ?? 'N/A',
            $sponsor->payment_configs->count(),
            $beneficiarios ?: 'Sin beneficiarios asignados',
            $sponsor->is_anonymous ? 'Sí' : 'No',
            $contacto = ($sponsor->contact_by === 'parent') ? 'Los Padres' : 'ENLAC',
            $sponsor->created_at->format('d/m/Y')
        ];
    }
}
