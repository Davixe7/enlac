<?php

namespace App\Http\Controllers;

use App\Http\Resources\EquinotherapyTransferResource;
use App\Models\EquinotherapyTransfer;
use Illuminate\Http\Request;

class EquinotherapyTransferController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $transfers = EquinotherapyTransfer::with('candidate')
            ->whereDate('date', $date)
            ->get();

        return EquinotherapyTransferResource::collection($transfers);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'candidate_id' => 'required|exists:candidates,id',
            'ida' => 'nullable|date_format:H:i',
            'regreso' => 'nullable|date_format:H:i'
        ]);

        $transfer = EquinotherapyTransfer::updateOrCreate(
            ['date' => $data['date'], 'candidate_id' => $data['candidate_id']],
            $data
        );

        return new EquinotherapyTransferResource($transfer->load('candidate'));
    }

}
