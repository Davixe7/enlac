<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTransportRequest;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Beneficiary;
use App\Models\Candidate;
use App\Models\User;
use App\Services\CandidateService;
use Illuminate\Http\Request;
use App\Notifications\BeneficiaryReadyToEnter;
use App\Notifications\BeneficiaryScheduleEntry;

class BeneficiaryController extends Controller
{

    protected $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$beneficiaries = Candidate::beneficiaries()->name($request->name)->orderBy('first_name')->get();

        $beneficiaries = Candidate::beneficiaries()
            ->whereNotIn('status', ['graduado', 'inactivo', 'exenlac', 'fallecido'])
            ->name($request->name)
            ->orderBy('first_name')
            ->with('program')
            ->get();

        return BeneficiaryResource::collection($beneficiaries);
    }
 
    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        return new BeneficiaryResource($candidate->load(['personal_groups']));
    }

    public function beneficiariesWithEquinetherapyPlans(Request $request){
        $beneficiaries = Candidate::beneficiariesEquinetherapyActivePlan()->get();

        return BeneficiaryResource::collection($beneficiaries);
    }

    public function updateEquineTherapyPermissions(Candidate $candidate, Request $request){
        $data = $request->only(['equinetherapy_permission_medical', 'equinetherapy_permission_legal_guardian']);
        $candidate->update($data);
        return response()->json([], 200);
    }

    public function changeStatus(Request $request, Candidate $candidate)
    {
        if ($candidate->admission_status !== 1) {
            return response()->json(['error' => 'Solo beneficiarios pueden cambiar estatus'], 400);
        }

        $request->validate([
            'status'   => 'required|string|in:pendiente_ingresar,programar_ingreso,ingreso_programado,listo_ingresar,activo,inactivo,graduado,permiso_temporal,exenlac,fallecido,prueba_vida',
            'comment'  => 'required|string',
            'document' => 'nullable|file|max:4096',
            'program_id' => 'nullable|integer',
            'scheduled_entry_date' => 'nullable|date', // 👈 nuevo campo
            'observations' => 'nullable|string'
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('beneficiary_documents', 'public');
        }

        // Cambio normal
        $candidate->changeStatus($request->status, $request->comment, $documentPath);

        // Notificación especial
        /* if ($request->status === 'listo_ingresar') {
            $users = User::role('coord_physical')->get();
            foreach ($users as $user) {
                $user->notify(new BeneficiaryReadyToEnter($candidate));
            }
        } */

        if ($request->status === 'programar_ingreso') {
            $candidate->changeStatus('ingreso_programado', $request->comment, $documentPath);

            $programName = $candidate->program?->name ?? 'Sin programa';
            $scheduledDate = $request->input('scheduled_entry_date', now()->toDateString());
            $observations = $request->input('observations');

            $candidate->scheduled_entry_date = $scheduledDate;
            $candidate->save();

            /* $users = User::role('coord_physical')->get();
            foreach ($users as $user) {
                $user->notify(new BeneficiaryScheduleEntry($candidate, $programName, $scheduledDate, $observations));
            } */
        }

        return new BeneficiaryResource($candidate->load(['statusHistory', 'personal_groups', 'program']));
    }

    public function reingreso(Candidate $candidate, Request $request)
    {
        if ($candidate->status !== 'inactivo') {
            return response()->json(['error' => 'El beneficiario no está inactivo'], 400);
        }

        $comment = $request->input('comment', 'Reingreso desde reporte');
        $candidate->changeStatus('activo', $comment);

        return new BeneficiaryResource($candidate->load(['statusHistory', 'personal_groups', 'program']));
    }

}
