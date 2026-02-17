<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Models\ActivityDailyScore;
use App\Models\Attendance;
use App\Models\Candidate;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryIndividualReportController extends Controller
{
    public function index(Request $request, Candidate $candidate)
    {
        $candidate->load(['program', 'enlacResponsible']);
        $start = now()->startOfYear()->addMonths($request->month - 1);
        $end = $start->copy()->addMonths(5)->endOfMonth();
        $year = now()->year;
        $periodLabel = ucfirst($start->locale('es')->shortMonthName) . '-' . ucfirst($end->locale('es')->shortMonthName);
        $daysCount = $start->diffInDaysFiltered(function ($day) {
            return !$day->isWeekend();
        }, $end);

        $attendances = Attendance::selectRaw("
                SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendances.status = 'absent'  AND attendances.absence_justification_type IS NULL THEN 1 ELSE 0 END) as unjustified,
                SUM(CASE WHEN attendances.status = 'absent'  AND attendances.absence_justification_type IS NOT NULL THEN 1 ELSE 0 END) as justified
            ")
            ->where('attendances.type', 'daily')
            ->where('attendances.candidate_id', $candidate->id)
            ->groupBy('attendances.candidate_id')
            ->whereBetween('attendances.date', [$start, $end])
            ->first();

        $issues = Issue::selectRaw("COUNT(*) as total")
            ->where('issues.candidate_id', $candidate->id)
            ->whereBetween('issues.date', [$start, $end])
            ->groupBy('candidate_id')
            ->value('total');

        $monthsRange = collect(range(0, 5))->map(function ($offset) use ($start) {
            return $start->month + $offset;
        });

        $rides = DB::table('rides')
            ->select('type')
            ->when($monthsRange, function ($query) use ($monthsRange, $year) {
                foreach ($monthsRange as $index => $monthNum) {
                    $alias = "m" . ($index + 1);
                    $query->selectRaw("
                        SUM(
                            CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? 
                            THEN (
                                (CASE WHEN departure_time IS NOT NULL THEN 1 ELSE 0 END) + 
                                (CASE WHEN return_time IS NOT NULL THEN 1 ELSE 0 END)
                            ) 
                            ELSE 0 END
                        ) as $alias
                    ", [$monthNum, $year]);
                }
            })
            ->whereBetween('rides.date', [$start, $end])
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $rides = $rides->map(function ($item) {
            $item->total = collect($item)->except('type')->sum();
            return $item;
        });

        $areaAttendances = DB::table('attendances')
            ->select('plan_category_id')
            ->when($monthsRange, function ($query) use ($monthsRange, $year) {
                foreach ($monthsRange as $index => $monthNum) {
                    $alias = "m" . ($index + 1);
                    $query->selectRaw("
                        SUM(
                            CASE WHEN MONTH(date) = ? AND YEAR(date) = ? 
                            THEN 1
                            ELSE 0 END
                        ) as $alias
                    ", [$monthNum, $year]);
                }
            })
            ->whereType('area')
            ->whereStatus('present')
            ->whereBetween('attendances.date', [$start, $end])
            ->groupBy('plan_category_id')
            ->get()
            ->keyBy('plan_category_id')
            ->map(function ($item) {
                $item->total = 0;
                foreach (range(1, 6) as $m) {
                    $key = "m{$m}";
                    $item->total = $item->total + $item->{$key};
                }
                return $item;
            });

        $appointments = DB::table('appointments')
            ->select('type_id')
            ->when($monthsRange, function ($query) use ($monthsRange, $year) {
                foreach ($monthsRange as $index => $monthNum) {
                    $alias = "m" . ($index + 1);
                    $query->selectRaw("
                        SUM(
                            CASE WHEN MONTH(date) = ? AND YEAR(date) = ? 
                            THEN 1
                            ELSE 0 END
                        ) as $alias
                    ", [$monthNum, $year]);
                }
            })
            ->whereCandidateId($candidate->id)
            ->whereBetween('appointments.date', [$start, $end])
            ->groupBy('type_id')
            ->get()
            ->keyBy('type_id')
            ->map(function ($item) {
                $item->total = 0;
                foreach (range(1, 6) as $m) {
                    $key = "m{$m}";
                    $item->total = $item->total + $item->{$key};
                }
                return $item;
            });

        $totalsRow = [];
        foreach (range(1, 6) as $m) {
            $key = "m{$m}";
            $totalAreas = $areaAttendances->sum($key);
            $totalAppts = $appointments->sum($key);
            $totalsRow[$key] = $totalAreas + $totalAppts;
        }

        return response()->json([
            'data' => compact(
                'candidate',
                'issues',
                'attendances',
                'daysCount',
                'periodLabel',
                'rides',
                'areaAttendances',
                'totalsRow',
                'appointments'
            )
        ]);
    }

    public function scores(Request $request, Candidate $candidate)
    {
        $start = now()->startOfYear()->addMonths($request->month - 1);
        $end = $start->copy()->addMonths(5)->endOfMonth();
        $startMonth = $start->month;
        $endMonth = $end->month;

        $rawResults = ActivityDailyScore::join('activity_plan', 'activity_plan.id', '=', 'activity_daily_scores.activity_plan_id')
            ->join('plans', 'plans.id', '=', 'activity_plan.plan_id')
            ->join('plan_categories', 'plan_categories.id', '=', 'plans.category_id')
            ->selectRaw("
            plans.category_id as category_id,
            MONTH(date) as month,
            SUM( IF(activity_daily_scores.color = 'positive', 1, 0) ) as positive,
            SUM( IF(activity_daily_scores.color = 'negative', 1, 0) ) as negative,
            SUM( IF(activity_daily_scores.color = 'warning', 1, 0) )  as warning
        ")
            ->where('candidate_id', $candidate->id)
            ->whereBetween('activity_daily_scores.date', [$start, $end])
            ->groupBy('plans.category_id', 'month')
            ->get()
            ->groupBy('category_id');

        $data = $rawResults->map(function ($items, $categoryId) use ($startMonth) {
            $formattedCategory = [];
            for ($i = 1; $i <= 6; $i++) {
                $actualMonth = $startMonth + ($i - 1);
                $found = $items->firstWhere('month', $actualMonth);

                $formattedCategory[$i] = [
                    "category_id" => (int)$categoryId,
                    "month"       => $i, // Índice agnóstico (1 al 6)
                    "real_month"  => $actualMonth, // Opcional, por si te sirve de debug
                    "positive"    => $found ? (string)$found->positive : "0",
                    "negative"    => $found ? (string)$found->negative : "0",
                    "warning"     => $found ? (string)$found->warning : "0",
                ];
            }

            $formattedCategory['total'] = [
                "positive"   => collect($formattedCategory)->sum('positive'),
                "negative"   => collect($formattedCategory)->sum('negative'),
                "warning"    => collect($formattedCategory)->sum('warning')
            ];

            return $formattedCategory;
        });

        $totals = [];
        for ($i = 1; $i <= 6; $i++) {
            $totals[$i] = [
                "id" => "total",
                "month"       => $i,
                "positive"    => $data->sum("$i.positive"),
                "negative"    => $data->sum("$i.negative"),
                "warning"     => $data->sum("$i.warning"),
            ];
        }
        $data['totals'] = $totals;

        return response()->json(compact('data'));
    }
}
