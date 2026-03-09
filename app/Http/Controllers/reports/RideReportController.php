<?php

namespace App\Http\Controllers\reports;

use App\Exports\RidesExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\RideResource;
use App\Models\Ride;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RideReportController extends Controller
{
    public function rubio(Request $request)
    {
        $startDate = $request->start_date ?: now()->subMonth()->format('Y-m-d');
        $endDate   = $request->end_date   ?: now()->format('Y-m-d');

        $data = Ride::whereType('rubio')
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['candidate' => function ($query) {
                $query
                    ->fullName()
                    ->with(['locationDetail', 'legalGuardian']);
            }])
            ->get();

        return RideResource::collection($data);
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'type'       => 'nullable|in:equine,rubio'
        ]);

        $query = Ride::whereType('rubio')->whereBetween('date', [$request->start_date, $request->end_date]);

        $fileName = 'reporte-traslados-' . $request->type . '-' . now()->format('d-m-Y') . '.xlsx';

        $range = $request->start_date . ' al ' . $request->end_date;
        return Excel::download(new RidesExport($query, $range), $fileName);
    }
}
