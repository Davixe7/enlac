<?php

namespace App\Exports;

use App\Models\PaymentPromise;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DonationExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;
    protected $type;

    /**
     * Variable para llevar el control de la última fuente impresa en Excel
     */
    protected $lastSource = null;

    public function __construct($query, string $type = 'general')
    {
        $this->query = $query;
        $this->type = $type;
    }

    public function query()
    {
        if ($this->type === 'cobranza') {
            // Carga de relaciones necesarias
            $query = $this->query->with(['donor', 'radiomarathonKey', 'procurationActivity']);

            // Orden estricto según la plantilla de ejemplo:
            // 1. Prospecto Previo / prospecto
            // 2. Llamada / llamadas
            // 3. Redes Sociales / redes_sociales
            // 4. Templete / templete
            // 5. Bazar / bazar
            // 6. Boteo / boteo
            // 7. Otro / otros
            return $query->orderByRaw("
                CASE LOWER(COALESCE(source, 'otro'))
                    WHEN 'prospecto' THEN 1
                    WHEN 'prospecto previo' THEN 1
                    WHEN 'llamada' THEN 2
                    WHEN 'llamadas' THEN 2
                    WHEN 'redes sociales' THEN 3
                    WHEN 'redes_sociales' THEN 3
                    WHEN 'templete' THEN 4
                    WHEN 'bazar' THEN 5
                    WHEN 'boteo' THEN 6
                    ELSE 7
                END ASC
            ")->orderBy('created_at', 'asc');
        }

        return $this->query
            ->with(['donor', 'sponsor'])
            ->orderBy('source', 'asc');
    }

    public function headings(): array
    {
        if ($this->type === 'cobranza') {
            return [
                'FUENTE',
                'NOMBRE DEL DONANTE',
                'EMPRESA',
                'ESTATUS DE PROSPECTO',
                'MONTO PROMESA DEL ÚLTIMO RADIOMARATÓN',
                'MONTO PAGADO DEL ÚLTIMO RADIOMARATÓN',
                'MONTO PROMESA ACTUAL',
                'FECHA PROMESA DE PAGO',
                'FECHA PUBLICADO',
                'HORA PUBLICADO',
                'TIPO DE DONATIVO',
                'MONTO PAGADO',
                'FECHA DE PAGO',
                'FORMA DE PAGO',
                'MONEDA',
                'TIPO DE CAMBIO',
                'EQUIVALENCIA EN PESOS',
                'TOTAL DONATIVO',
                'CLAVE DE RADIOMARATÓN',
                'REFERENCIA',
                'NO. FOLIO',
                'REQUIERE RECIBO DEDUCIBLE',
                'ANÓNIMO',
                'ESTATUS DE PAGO',
                'NO. BOTE',
                'RESPONSABLE DEL BOTE',
                'NOMBRE DEL QUE CONTÓ',
                'RECIBÍ DÓLARES',
            ];
        }

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
        if ($this->type === 'cobranza') {
            return $this->mapCobranza($donation);
        }

        return $this->mapGeneral($donation);
    }

    /**
     * Mapeo exclusivo para la exportación de Cobranza agrupado visualmente
     */
    protected function mapCobranza($donation): array
    {
        $donor = $donation->donor;
        $rawSource = strtolower(trim($donation->source ?? ''));

        // 1. Normalización del Nombre de la Fuente
        $fuenteLabel = 'Otro';

        if (in_array($rawSource, ['prospecto', 'prospecto previo'])) {
            $fuenteLabel = 'Prospecto Previo';
        } elseif (in_array($rawSource, ['llamada', 'llamadas'])) {
            $fuenteLabel = 'Llamada';
        } elseif (in_array($rawSource, ['rrss', 'redes_sociales'])) {
            $fuenteLabel = 'Redes Sociales';
        } elseif ($rawSource === 'templete') {
            $fuenteLabel = 'Templete';
        } elseif ($rawSource === 'bazar') {
            $fuenteLabel = 'Bazar';
        } elseif ($rawSource === 'boteo') {
            $fuenteLabel = 'Boteo';
        }

        // 2. Nombre del Donante y Empresa (Si es Boteo asigna 'N/A' por defecto si no hay donante registrado)
        $defaultDonorName = ($fuenteLabel === 'Boteo') ? 'N/A' : 'Público en General';
        $donorName = $defaultDonorName;
        $companyName = 'N/A';

        if ($donor) {
            $fullName = $donor->full_name ?? '';
            $donorName = trim(preg_replace('/\s+/', ' ', $fullName)) ?: ($donation->donor_name ?? $defaultDonorName);
            $companyName = $donor->company_name ?? 'N/A';
        } elseif (!empty($donation->donor_name)) {
            $donorName = $donation->donor_name;
        }

        // 3. Consulta de Promesa de Pago asociada al donante y actividad
        $promise = null;
        if ($donation->donor_id && $donation->procuration_activity_id) {
            $promise = PaymentPromise::where('donor_id', $donation->donor_id)
                ->where('procuration_activity_id', $donation->procuration_activity_id)
                ->first();
        }

        // 4. Extracción de datos de la Promesa
        $montoPromesaActual = $promise ? $promise->amount : 0.00;
        $fechaPromesaPago = $promise && $promise->date ? Carbon::parse($promise->date)->format('d/m/Y') : 'N/A';

        $publishedAt = $promise ? $promise->published_at : null;
        $fechaPublicado = $publishedAt ? Carbon::parse($publishedAt)->format('d/m/Y') : 'N/A';
        $horaPublicado  = $publishedAt ? Carbon::parse($publishedAt)->format('H:i') : 'N/A';

        $isAnonymous = ($promise && $promise->anonymous) ? 'Sí' : 'No';

        // 5. Estatus de Pago
        $estatusPago = 'Total';
        if ($montoPromesaActual > 0) {
            if ($donation->amount >= $montoPromesaActual) {
                $estatusPago = 'Total';
            } elseif ($donation->amount > 0) {
                $estatusPago = 'Parcial';
            } else {
                $estatusPago = 'Pendiente';
            }
        }

        // 6. Estatus de Prospecto
        $estatusProspecto = 'N/A';
        if ($publishedAt) {
            $estatusProspecto = 'Publicado';
        } elseif ($promise || $fuenteLabel === 'Prospecto Previo') {
            $estatusProspecto = 'Prospecto';
        }

        // 7. Agrupación Visual: Mostrar el texto de FUENTE sólo si cambió con respecto a la fila anterior
        if ($this->lastSource === $fuenteLabel) {
            $fuenteDisplay = '';
        } else {
            $fuenteDisplay = $fuenteLabel;
            $this->lastSource = $fuenteLabel;
        }

        // 8. Clave de Radiomaratón (Propiedad 'code' del modelo RadiomarathonKey)
        $claveRadiomaraton = $donation->radiomarathonKey ? $donation->radiomarathonKey->code : 'N/A';

        return [
            $fuenteDisplay,                                                                     // 1. FUENTE (Aparece 1 sola vez por grupo)
            $donorName,                                                                          // 2. NOMBRE DEL DONANTE ('N/A' en Boteo)
            $companyName,                                                                        // 3. EMPRESA
            $estatusProspecto,                                                                   // 4. ESTATUS DE PROSPECTO
            0.00,                                                                                // 5. MONTO PROMESA DEL ÚLTIMO RADIOMARATÓN
            0.00,                                                                                // 6. MONTO PAGADO DEL ÚLTIMO RADIOMARATÓN
            $montoPromesaActual,                                                                 // 7. MONTO PROMESA ACTUAL
            $fechaPromesaPago,                                                                   // 8. FECHA PROMESA DE PAGO
            $fechaPublicado,                                                                     // 9. FECHA PUBLICADO
            $horaPublicado,                                                                      // 10. HORA PUBLICADO
            $donation->donation_type ?? 'Efectivo',                                              // 11. TIPO DE DONATIVO
            $donation->amount,                                                                   // 12. MONTO PAGADO
            $donation->payment_date ? Carbon::parse($donation->payment_date)->format('d/m/Y') : 'N/A', // 13. FECHA DE PAGO
            $donation->payment_method ?? 'N/A',                                                  // 14. FORMA DE PAGO
            $donation->currency ?? 'MXN',                                                        // 15. MONEDA
            $donation->exchange_rate ?? 1.0000,                                               // 16. TIPO DE CAMBIO
            $donation->equivalent_amount_mxn ?? $donation->amount,                               // 17. EQUIVALENCIA EN PESOS
            $donation->amount,                                                                   // 18. TOTAL DONATIVO
            $claveRadiomaraton,                                                                  // 19. CLAVE DE RADIOMARATÓN
            $donation->reference ?? 'N/A',                                                       // 20. REFERENCIA
            $donation->folio_number ?? 'N/A',                                                    // 21. NO. FOLIO
            $donation->has_tax_receipt ? 'Sí' : 'No',                                            // 22. REQUIERE RECIBO DEDUCIBLE
            $isAnonymous,                                                                        // 23. ANÓNIMO
            $estatusPago,                                                                        // 24. ESTATUS DE PAGO
            $donation->boteo_can_number ?? 'N/A',                                                // 25. NO. BOTE
            $donation->boteo_responsible_name ?? 'N/A',                                          // 26. RESPONSABLE DEL BOTE
            $donation->boteo_counter_name ?? 'N/A',                                              // 27. NOMBRE DEL QUE CONTÓ
            strtoupper($donation->currency ?? 'MXN') === 'USD' ? 'Sí' : 'No',                    // 28. RECIBÍ DÓLARES
        ];
    }

    /**
     * Mapeo estándar para los demás tipos de exportación
     */
    protected function mapGeneral($donation): array
    {
        $donor = $donation->donor;
        $sponsor = $donation->sponsor;

        $donorName = 'Público General / N/A';
        $companyName = 'N/A';

        if ($donor) {
            $fullName = $donor->full_name ?? '';
            $donorName = trim(preg_replace('/\s+/', ' ', $fullName)) ?: 'N/A';
            $companyName = $donor->company_name ?? 'N/A';
        } elseif ($sponsor) {
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
