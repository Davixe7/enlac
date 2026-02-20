<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use App\Http\Resources\BeneficiaryReportsResource;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Candidate;
use App\Models\Activity;
use App\Models\Plan;

use App\Services\CandidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if( $request->type == 'search' ){
            $data = DB::table('candidates')
            ->whereIn('status', [CandidateStatus::ACCEPTED, CandidateStatus::SCHEDULED, CandidateStatus::READY, CandidateStatus::ACTIVE])
            ->select('id as value')
            ->selectRaw("CONCAT_WS(' ', first_name, middle_name, last_name) as label")
            ->get();
            return response()->json(compact('data'), 200);
        }

        $beneficiaries = Candidate::name($request->name)
            ->with(['program'])
            ->beneficiaries();

        if( $request->equinetherapy == 1 ){
            $beneficiaries = $beneficiaries->equinetherapyActivePlan();
        }

        if( $request->activity_id  ||  $request->category_id ){

            $groups = Plan::hasActivity($request->activity_id)
            ->filterByCat($request->category_id)
            ->where('start_date', '<=', now()->format('Y-m-d'))
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->pluck('group_id');

            $data = Candidate::name($request->name)
            ->whereHas('groups', fn($q)=>$q->whereIn('groups.id', $groups))->get();

            return BeneficiaryResource::collection($data);
        }

        $beneficiaries = $beneficiaries->orderBy('first_name')->get();

        return BeneficiaryResource::collection($beneficiaries);
    }
 
    public function show(Candidate $candidate)
    {
        return new BeneficiaryResource($candidate->load(['program','personal_groups', 'locationDetail']));
    }

    public function updateEquineTherapyPermissions(Candidate $candidate, Request $request){
        $data = $request->only(['equinetherapy_permission_medical', 'equinetherapy_permission_legal_guardian']);
        $candidate->update($data);
        return response()->json([], 200);
    }

    public function reingreso(Candidate $candidate, Request $request)
    {
        $comment = $request->input('comment', 'Reingreso desde reporte');
        $candidate->changeStatus('activo', $comment);

        return new BeneficiaryResource($candidate->load(['statusHistory', 'personal_groups', 'program']));
    }

    public function reports(Request $request){

        $beneficiaries = Candidate::whereIn('status', [
                CandidateStatus::GRADUATED,
                CandidateStatus::DECEASED,
                CandidateStatus::EX_ENLAC,
            ])
            ->orderBy('first_name', 'ASC')
            ->with(['program'])
            ->get();

        $counts = $beneficiaries
        ->groupBy('status')
        ->map(fn ($group) => $group->count());

        return new BeneficiaryReportsResource([
            'beneficiaries' => $beneficiaries,
            'counts' => $counts,
        ]);
    }
}
