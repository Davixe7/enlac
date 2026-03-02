<?php

namespace App\Http\Controllers\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExcecutiveReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?: now()->year;

        $rides = Candidate::select('candidates.id')
        ->join('rides', 'rides.candidate_id', '=', 'candidates.id')
        ->selectRaw("
            SUM(CASE WHEN type = 'equine' THEN
                (CASE WHEN departure_time IS NOT NULL THEN 1 ELSE 0 END) +
                (CASE WHEN return_time IS NOT NULL THEN 1 ELSE 0 END)
            ELSE 0 END) as equine,
            SUM(CASE WHEN type = 'rubio' THEN
                (CASE WHEN departure_time IS NOT NULL THEN 1 ELSE 0 END) +
                (CASE WHEN return_time IS NOT NULL THEN 1 ELSE 0 END)
            ELSE 0 END) as rubio
        ")
        ->whereYear('date', $year)
        ->groupBy('candidates.id');

        if ($request->filled('candidate_id')) {
            $rides = $rides->where('candidates.id', $request->candidate_id);
        }

        $rides = $rides->get()->keyBy('id');

        $issues = Candidate::select('candidates.id')
            ->join('issues', 'issues.candidate_id', '=', 'candidates.id')
            ->selectRaw("COUNT(*) as total")
            ->whereYear('issues.date', $year)
                ->groupBy('candidates.id');

            if ($request->filled('candidate_id')) {
                $issues = $issues->where('candidates.id', $request->candidate_id);
            }

            $issues = $issues->get()->keyBy('id');

        $attendances = Candidate::select('candidates.id')
            ->join('attendances', 'attendances.candidate_id', '=', 'candidates.id')
            ->selectRaw("
            SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN attendances.status = 'absent'  AND attendances.absence_justification_type IS NULL THEN 1 ELSE 0 END) as unjustified,
            SUM(CASE WHEN attendances.status = 'absent'  AND attendances.absence_justification_type IS NOT NULL THEN 1 ELSE 0 END) as justified
        ")
            ->where('attendances.type', 'daily')
            ->whereYear('attendances.date', $year)
            ->groupBy('candidates.id');

        if ($request->filled('candidate_id')) {
            $attendances = $attendances->where('attendances.candidate_id', $request->candidate_id);
        }

        $attendances = $attendances->get()->keyBy('id');

        $scores = Candidate::select('candidates.id', 'candidates.first_name', 'candidates.middle_name', 'candidates.last_name')
            ->join('activity_daily_scores as ads', 'candidates.id', '=', 'ads.candidate_id')
            ->whereYear('ads.date', $year)
            ->selectRaw("
                CONCAT_WS(' ', candidates.first_name, candidates.middle_name, candidates.last_name) as full_name,
                DATE_FORMAT(ads.date, '%m') as month,
                SUM(CASE WHEN ads.color = 'positive' THEN 1 ELSE 0 END) as positive,
                SUM(CASE WHEN ads.color = 'warning' THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN ads.color = 'negative' THEN 1 ELSE 0 END) as negative
            ")
            ->groupBy('candidates.id', 'candidates.first_name', 'full_name', 'month');

        if ($request->filled('candidate_id')) {
            $scores = $scores->where('ads.candidate_id', $request->candidate_id);
        }

        $scores = $scores->get()->groupBy('id');

        $data = $scores->map(function ($candidateGroup, $id) use ($attendances, $issues, $rides) {
            $att = $attendances->get($id);
            $iss = $issues->get($id);
            $rds = $rides->get($id);

            return [
                'full_name'   => $candidateGroup->first()->full_name,
                'present'     => $att->present ?? 0,
                'justified'   => $att->justified ?? 0,
                'unjustified' => $att->unjustified ?? 0,
                'issues'      => $iss ? $iss->total  : 0,
                'rubio'       => $rds ? $rds->rubio  : 0,
                'equine'      => $rds ? $rds->equine : 0,
                'months'      => $candidateGroup->keyBy('month')
            ];
        })->values();
        return response()->json(compact('data'));
    }

    public function export(Request $request)
    {
        // 1. Definir el año del reporte
        $year = $request->year ?: now()->year;

        // 2. Consulta de Transportes (Rides)
        $rides = Candidate::select('candidates.id')
            ->join('rides', 'rides.candidate_id', '=', 'candidates.id')
            ->selectRaw("
                SUM(CASE WHEN type = 'equine' THEN
                    (CASE WHEN departure_time IS NOT NULL THEN 1 ELSE 0 END) +
                    (CASE WHEN return_time IS NOT NULL THEN 1 ELSE 0 END)
                ELSE 0 END) as equine,
                SUM(CASE WHEN type = 'rubio' THEN
                    (CASE WHEN departure_time IS NOT NULL THEN 1 ELSE 0 END) +
                    (CASE WHEN return_time IS NOT NULL THEN 1 ELSE 0 END)
                ELSE 0 END) as rubio
            ")
            ->whereYear('date', $year)
            ->groupBy('candidates.id');

        if ($request->filled('candidate_id')) {
            $rides = $rides->where('candidates.id', $request->candidate_id);
        }
        $rides = $rides->get()->keyBy('id');

        // 3. Consulta de Incidencias (Issues)
        $issues = Candidate::select('candidates.id')
            ->join('issues', 'issues.candidate_id', '=', 'candidates.id')
            ->selectRaw("COUNT(*) as total")
            ->whereYear('issues.date', $year)
            ->groupBy('candidates.id');

        if ($request->filled('candidate_id')) {
            $issues = $issues->where('candidates.id', $request->candidate_id);
        }
        $issues = $issues->get()->keyBy('id');

        // 4. Consulta de Asistencias (Attendances)
        $attendances = Candidate::select('candidates.id')
            ->join('attendances', 'attendances.candidate_id', '=', 'candidates.id')
            ->selectRaw("
                SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendances.status = 'absent' AND attendances.absence_justification_type IS NULL THEN 1 ELSE 0 END) as unjustified,
                SUM(CASE WHEN attendances.status = 'absent' AND attendances.absence_justification_type IS NOT NULL THEN 1 ELSE 0 END) as justified
            ")
            ->where('attendances.type', 'daily')
            ->whereYear('attendances.date', $year)
            ->groupBy('candidates.id');

        if ($request->filled('candidate_id')) {
            $attendances = $attendances->where('attendances.candidate_id', $request->candidate_id);
        }
        $attendances = $attendances->get()->keyBy('id');

        // 5. Consulta de Semáforos/Puntajes (Scores)
        $scores = Candidate::select('candidates.id', 'candidates.first_name', 'candidates.middle_name', 'candidates.last_name')
            ->join('activity_daily_scores as ads', 'candidates.id', '=', 'ads.candidate_id')
            ->whereYear('ads.date', $year)
            ->selectRaw("
                CONCAT_WS(' ', candidates.first_name, candidates.middle_name, candidates.last_name) as full_name,
                DATE_FORMAT(ads.date, '%m') as month,
                SUM(CASE WHEN ads.color = 'positive' THEN 1 ELSE 0 END) as positive,
                SUM(CASE WHEN ads.color = 'warning' THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN ads.color = 'negative' THEN 1 ELSE 0 END) as negative
            ")
            ->groupBy('candidates.id', 'candidates.first_name', 'candidates.middle_name', 'candidates.last_name', 'month');

        if ($request->filled('candidate_id')) {
            $scores = $scores->where('ads.candidate_id', $request->candidate_id);
        }
        $scores = $scores->get()->groupBy('id');

        // 6. Unión de datos (Data Mapping)
        $data = $scores->map(function ($candidateGroup, $id) use ($attendances, $issues, $rides) {
            $att = $attendances->get($id);
            $iss = $issues->get($id);
            $rds = $rides->get($id);

            return [
                'full_name'   => $candidateGroup->first()->full_name,
                'present'     => $att->present ?? 0,
                'justified'   => $att->justified ?? 0,
                'unjustified' => $att->unjustified ?? 0,
                'issues'      => $iss ? $iss->total : 0,
                'rubio'       => $rds ? $rds->rubio : 0,
                'equine'      => $rds ? $rds->equine : 0,
                'months'      => $candidateGroup->keyBy('month')
            ];
        })->values();

        // 7. Generar el Excel
        $fileName = 'Reporte_Ejecutivo_Anual_' . $year . '_' . now()->format('d-m-Y_His') . '.xlsx';

        return Excel::download(new \App\Exports\ExecutiveReportExport($data, $year), $fileName);
    }
}
