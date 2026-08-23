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

class RaffleTicketsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $raffle;

    public function __construct($raffle)
    {
        $this->raffle = $raffle;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'REPORTE DE BOLETOS - ' . mb_strtoupper($this->raffle->name));

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'FECHA DE EMISIÓN: ' . now()->format('d/m/Y H:i'));

                $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
            },
        ];
    }

    public function collection()
    {
        return $this->raffle->tickets()->with(['seller', 'buyer', 'donations'])->get();
    }

    public function headings(): array
    {
        return [
            '# Boleto',
            'Estatus',
            'Vendedor',
            'Fecha de Venta',
            'Comprador',
            'Vaquita',
            'Recibo Deducible',
            'Cobranza ENLAC',
            'Monto Pagado',
            'Nro. Recibo'
        ];
    }

    public function map($ticket): array
    {
        $statuses = [
            'available' => 'Disponible',
            'sold'      => 'Vendido',
            'won'       => 'Ganador',
            'discarded' => 'Descartado'
        ];

        $firstDonation = $ticket->donations->first();
        $receiptNumber = $firstDonation ? ($firstDonation->payment_id ?? $firstDonation->id) : '';

        return [
            $ticket->number,
            $statuses[$ticket->status] ?? $ticket->status,
            $ticket->seller ? $ticket->seller->first_name : '',
            $ticket->sold_at ?? 'Pendiente',
            $ticket->buyer ? $ticket->buyer->first_name : '',
            $ticket->cow ? 'Sí' : 'No',
            $ticket->deductible_receipt ? 'Sí' : 'No',
            $ticket->enlac_collection ? 'Sí' : 'No',
            '$ ' . number_format($ticket->donations_sum_amount ?? $ticket->donations->sum('amount'), 2),
            $receiptNumber
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }
}
