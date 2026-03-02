<?php

namespace App\Exports;
use App\Models\Issue;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IssuesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        // Ejecutamos la consulta con las relaciones para evitar el problema de N+1
        return $this->query->with(['user', 'plan_category', 'candidate'])->get();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Beneficiario',
            'Categoría de Plan',
            'Usuario / Responsable',
            'Tipo de Incidencia',
            'Comentarios',
        ];
    }

    public function map($issue): array
    {
        return [
            $issue->id,
            Carbon::parse($issue->date)->format('d/m/Y'),
            $issue->candidate->full_name ?? 'No disponible',
            $issue->plan_category->name ?? 'No disponible',
            $issue->user->name ?? 'No disponible',
            $issue->type,
            $issue->comments,
        ];
    }
}
