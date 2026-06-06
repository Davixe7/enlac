<?php

namespace App\Http\Controllers;

use App\Models\DonorFiscalRecord;
use App\Http\Requests\StoreDonorFiscalRecordRequest;
use App\Http\Resources\DonorFiscalRecordResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DonorFiscalRecordController extends Controller
{
    /**
     * Muestra el listado de razones sociales filtradas por donante.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'donor_id' => 'required|exists:donors,id'
        ]);

        $records = DonorFiscalRecord::where('donor_id', $request->donor_id)
            ->latest()
            ->get();

        return DonorFiscalRecordResource::collection($records);
    }

    /**
     * Almacena una nueva razón social fiscal.
     */
    public function store(StoreDonorFiscalRecordRequest $request): DonorFiscalRecordResource
    {
        $record = DonorFiscalRecord::create($request->validated());

        return new DonorFiscalRecordResource($record);
    }

    /**
     * Muestra una razón social específica.
     */
    public function show(DonorFiscalRecord $fiscalRecord): DonorFiscalRecordResource
    {
        return new DonorFiscalRecordResource($fiscalRecord);
    }

    /**
     * Actualiza un registro fiscal existente.
     */
    public function update(StoreDonorFiscalRecordRequest $request, DonorFiscalRecord $fiscalRecord)
    {
        $fiscalRecord->update($request->validated());
        $fiscalRecord->refresh();

        return new DonorFiscalRecordResource($fiscalRecord);
    }

    /**
     * Elimina un registro fiscal.
     */
    public function destroy($id)
    {
        $fiscalRecord = DonorFiscalRecord::findOrFail($id);

        $fiscalRecord->delete();
        return response()->json(['message' => 'Registro eliminado con éxito'], 200);
    }
}
