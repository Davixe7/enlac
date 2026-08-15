<?php

namespace App\Http\Controllers;

use App\Models\RadiomarathonCall;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RadiomarathonCallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = RadiomarathonCall::where('procuration_activity_id', $request->procuration_activity_id)->get();
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'procuration_activity_id' => 'required|exists:procuration_activities,id',
            'donor_name'              => 'required',
            'donation_type'           => 'required',
            'address'                 => 'required',
            'amount'                  => 'numeric',
            'collector'               => 'required',
            'has_tax_receipt'         => 'sometimes|boolean',
            'paid'                    => 'sometimes|boolean'
        ]);

        $data = RadiomarathonCall::create($data);
        return response()->json(compact('data'));
    }

    /**
     * Display the specified resource.
     */
    public function show(RadiomarathonCall $radiomarathonCall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RadiomarathonCall $radiomarathonCall)
    {
        $data = $request->validate([
            'procuration_activity_id' => 'sometimes|exists:procuration_activities,id',
            'donor_name'              => 'sometimes|string',
            'donation_type'           => 'sometimes|string',
            'address'                 => 'sometimes|string',
            'amount'                  => 'sometimes|numeric',
            'collector'               => 'sometimes|string',
            'has_tax_receipt'         => 'sometimes|boolean',
            'paid'                    => 'sometimes|boolean'
        ]);

        $data = $radiomarathonCall->update($data);
        return response()->json(compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RadiomarathonCall $radiomarathonCall)
    {
        //
    }

    public function storeAndPrint(Request $request)
    {
        $request->validate([
            'donor_name'    => 'required|string',
            'address'       => 'required|string',
            'donation_type' => 'required|string',
        ]);

        // 1. Guardar la llamada en la base de datos
        $call = RadiomarathonCall::create($request->all());

        // 2. Generar el PDF
        $pdf = Pdf::loadView('pdf.radiomarathon_call_receipt', compact('call'))
                ->setPaper([0, 0, 226.77, 368.5], 'portrait');

        // 3. Retornar descarga del archivo
        return $pdf->download('folio_llamada_' . $call->id . '.pdf');
    }
}
