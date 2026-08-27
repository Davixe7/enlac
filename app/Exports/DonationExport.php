<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DonationExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;
    protected $type;

    public function __construct($query, string $type = 'general')
    {
        $this->query = $query;
        $this->type = $type;
    }

    public function query()
    {
        // Cargar ambas relaciones de manera ansiosa para evitar N+1
        return $this->query->with(['donor', 'sponsor']);
    }

    public function headings(): array
    {
        $headings = [
            'Folio',
            'Donante / Padrino',
            'Empresa / Institución',
            'Concepto',
            'Monto',
            'Moneda',
            'Monto Equiv. MXN',
            'Método de Pago',
            'Fecha de Pago',
            'Origen (Source)',
            'Tipo de Donación',
            '¿Deducible?',
            'No. Recibo Fiscal',
        ];

        if ($this->type === 'boteo') {
            $headings = array_merge($headings, [
                'Área de Boteo',
                'No. de Bote',
                'Responsable',
                'Contador',
                '10% Boteo',
            ]);
        }

        return $headings;
    }

    public function map($donation): array
    {
        $donor = $donation->donor;
        $sponsor = $donation->sponsor;

        $donorName = 'Público General / N/A';
        $companyName = 'N/A';

        // 1. Prioridad: Donante registrado (Donor)
        if ($donor) {
            $fullName = $donor->full_name ?? '';
            $donorName = trim(preg_replace('/\s+/', ' ', $fullName)) ?: 'N/A';
            $companyName = $donor->company_name ?? 'N/A';
        }
        // 2. Segunda opción: Padrino registrado (Sponsor) usando su accesor full_name
        elseif ($sponsor) {
            $fullName = $sponsor->full_name ?? '';
            $donorName = trim(preg_replace('/\s+/', ' ', $fullName)) ?: 'N/A';
            $companyName = $sponsor->company_name ?? 'N/A';
        }

        $data = [
            $donation->folio_number,
            $donorName,
            $companyName,
            $donation->concept ?? 'N/A',
            $donation->amount,
            $donation->currency,
            $donation->equivalent_amount_mxn ?? $donation->amount,
            $donation->payment_method,
            $donation->payment_date ? Carbon::parse($donation->payment_date)->format('d/m/Y') : 'N/A',
            $donation->source ?? 'N/A',
            $donation->donation_type ?? 'N/A',
            $donation->has_tax_receipt ? 'Sí' : 'No',
            $donation->tax_receipt_number ?? 'N/A',
        ];

        if ($this->type === 'boteo') {
            $data = array_merge($data, [
                $donation->boteo_area ?? 'N/A',
                $donation->boteo_can_number ?? 'N/A',
                $donation->boteo_responsible_name ?? 'N/A',
                $donation->boteo_counter_name ?? 'N/A',
                $donation->boteo_ten_percent ?? 0.00,
            ]);
        }

        return $data;
    }
}
