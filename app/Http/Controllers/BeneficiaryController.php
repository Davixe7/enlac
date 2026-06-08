<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Enums\CandidateStatus;
use App\Http\Resources\BeneficiaryReportsResource;
use App\Http\Resources\BeneficiaryResource;
use App\Models\Candidate;
use App\Models\Activity;
use App\Models\Plan;
use App\Models\Appointment;

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
        if( $request->plan_type_id ){
            $data = Candidate::whereHas('groups.plans', function($query) use ($request) {
                $query->where('plan_type_id', $request->plan_type_id);
            })->get();
            return BeneficiaryResource::collection($data);
        }

        if( $request->group_id ){
            $data = Candidate::whereHas('groups', function($query) use ($request) {
                $query->where('id', $request->group_id);
            })->get();
            return BeneficiaryResource::collection($data);
        }

        if( $request->type == 'search' ){
            $data = DB::table('candidates')
            ->whereIn('status', [CandidateStatus::ACCEPTED, CandidateStatus::SCHEDULED, CandidateStatus::READY, CandidateStatus::ACTIVE])
            ->when($request->name, function($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->name . '%')
                    ->orWhere('middle_name', 'like', '%' . $request->name . '%')
                    ->orWhere('last_name', 'like', '%' . $request->name . '%');
                });
            })
            ->select('id as value')
            ->selectRaw("CONCAT_WS(' ', first_name, middle_name, last_name) as label")
            ->limit(15)
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

    public function showById(int $candidate_id, Request $request)
    {
        $Appointment = Appointment::with(['evaluator','candidate'])->where('candidate_id', $candidate_id)->get();
        $candidate = $Appointment[0]->candidate_id;
        $data1 = DB::table('candidates')
            ->join('programs', 'candidates.program_id', '=', 'programs.id')
            ->join('contacts', 'candidates.id', '=', 'contacts.candidate_id')
            ->join('brain_function_ranks', 'candidates.id', '=', 'brain_function_ranks.candidate_id')
            ->join("brain_levels", "brain_function_ranks.brain_level_id", "=", "brain_levels.id")
            ->join("brain_functions", "brain_function_ranks.brain_function_id", "=", "brain_functions.id")
            ->whereIn('candidates.id', [$candidate])
            ->selectRaw("CONCAT_WS(' ', candidates.first_name, candidates.middle_name, candidates.last_name) as candidate_name,candidates.entry_date,candidates.birth_date,candidates.diagnosis,programs.name as program_name,CONCAT_WS(' ', contacts.first_name, contacts.middle_name, contacts.last_name) as responsible_name, contacts.relationship, brain_levels.name as brain_level, brain_functions.name as brain_function")
            ->get();
        $hour = new \DateTime($Appointment[0]->date);

        // Obtener la URL de la imagen de perfil desde la tabla media (Spatie)
        $profileImage = DB::table('media')
            ->where('model_type', 'App\\Models\\Candidate')
            ->where('model_id', $candidate)
            ->where('collection_name', 'profile_picture')
            ->orderByDesc('id')
            ->first();
        $profileImageUrl = $profileImage ? asset('storage/' . $profileImage->id . '/' . $profileImage->file_name) : null;

        $data = [
            'candidate_id' => $candidate,
            'candidate_name' => $data1[0]->candidate_name,
            'entry_date' => $data1[0]->entry_date == null ? "--" : DATE('d/m/Y', strtotime($data1[0]->entry_date)),
            'birth_date' => $data1[0]->birth_date == null ? "--" : DATE('d/m/Y', strtotime($data1[0]->birth_date)),
            'diagnosis' => $data1[0]->diagnosis,
            'program_name' => $data1[0]->program_name,
            'responsible_name' => $data1[0]->responsible_name,
            'responsible_relationship' => $data1[0]->relationship,
            'brain_level' => $data1[0]->brain_level,
            'brain_function' => $data1[0]->brain_function,
            'hours_of_attention' => $hour->format('h:i A'),
            'date_of_attention' => DATE('d/m/Y', strtotime($Appointment[0]->date)),
            'profile_picture_url' => $profileImageUrl,
            'chronological_age'    => number_format( Carbon::parse($data1[0]->birth_date)->diffInMonths(), 2 )
        ];
        return response()->json(compact('data'), 200);
    }

    public function medicalRecords(int $idAppoiment, Request $request)
    {
        $medical_record = DB::table('medical_records')
            ->Where('appointment_id',"=", $idAppoiment)
            ->where('status', 1)
            ->selectRaw("id_medical_record,
                candidate_id,
                date_medical_record,
                date_soap,
                hereditary_family_history,
                non_pathological_personal_history,
                perinatal_history,
                andrological_gynecological_obstetric_history,
                medical_history,
                psychiatric_mental_status,
                nervous_system,
                respiratory_system,
                cardiovascular_system,
                digestive_system,
                genitourinary_system,
                musculoskeletal_system,
                endocrine_system,
                sensory_system,
                integumentary_system,
                weight,
                height,
                head_circumference,
                heart_rate,
                respiratory_rate,
                initial_weight,
                weight_age,
                height_age,
                weight_height,
                waist_cm,
                hip_cm,
                chest_cm,
                brain_perimeter_cm,
                brachial_circumference_cm,
                wrist_circumference_cm,
                calf_circumference_cm,
                other,
                imc,
                temperature,
                general_inspection,
                head,
                mental_status,
                hair,
                neck,
                thorax,
                abdomen,
                genitalia,
                anorectal,
                spine,
                upper_lower_limbs,
                peripheral_vascular_system,
                skin_appendages,
                areas_dryness_excessive_sweating,
                diagnostic_impression,
                treatment,
                case_analysis,
                created_at,
                updated_at,
                status,
                subjective,
                objective,
                assessment,
                plan,
                appointment_id,
                type_id,
                appointment_type
                ")
            ->get();
        if(count($medical_record) == 0){
            $data = [
                'id_medical_record' => 0,
                'candidate_id' => "",
                'date_medical_record' => DATE('Y-m-d'),
                'date_soap' => DATE('Y-m-d'),
                'hereditary_family_history' => "",
                'non_pathological_personal_history' => "",
                'perinatal_history' => "",
                'andrological_gynecological_obstetric_history' => "",
                'medical_history' => "",
                'psychiatric_mental_status' => "",
                'nervous_system' => "",
                'respiratory_system' => "",
                'cardiovascular_system' => "",
                'digestive_system' => "",
                'genitourinary_system' => "",
                'musculoskeletal_system' => "",
                'endocrine_system' => "",
                'sensory_system' => "",
                'integumentary_system' => "",
                'weight' => "",
                'height' => "",
                'head_circumference' => "",
                'heart_rate' => "",
                "initial_weight" => "",
                "weight_age" => "",
                "height_age" => "",
                "weight_height" => "",
                "waist_cm" => "",
                "hip_cm" => "",
                "chest_cm" => "",
                "brain_perimeter_cm" => "",
                "brachial_circumference_cm" => "",
                "wrist_circumference_cm" => "",
                "calf_circumference_cm" => "",
                "other" => "",
                "imc" => "",
                'respiratory_rate' => "",
                'temperature' => "",
                'general_inspection' => "",
                'head' => "",
                'mental_status' => "",
                'hair' => "",
                'neck' => "",
                'thorax' => "",
                'abdomen' => "",
                'genitalia' => "",
                'anorectal' => "",
                'spine' => "",
                'upper_lower_limbs' => "",
                'peripheral_vascular_system' => "",
                'skin_appendages' => "",
                'areas_dryness_excessive_sweating' => "",
                'diagnostic_impression' => "",
                'treatment' => "",
                'case_analysis' => "",
                'created_at' => "",
                'updated_at' => "",
                'status' => 0,
                'subjective' => "",
                'objective' => "",
                'assessment' => "",
                'plan' => "",
                'appointment_id' => $idAppoiment,
                'type_id' => "",
                'appointment_type' => ""
            ];
        }else{
            $data = [
                'id_medical_record' => $medical_record[0]->id_medical_record,
                'candidate_id' => $medical_record[0]->candidate_id,
                'date_medical_record' => $medical_record[0]->date_medical_record == null ? DATE('Y-m-d') : DATE('Y-m-d', strtotime($medical_record[0]->date_medical_record)),
                'date_soap' => $medical_record[0]->date_soap == null ? DATE('Y-m-d') : DATE('Y-m-d', strtotime($medical_record[0]->date_soap)),
                'hereditary_family_history' => $medical_record[0]->hereditary_family_history,
                'non_pathological_personal_history' => $medical_record[0]->non_pathological_personal_history,
                'perinatal_history' => $medical_record[0]->perinatal_history,
                'andrological_gynecological_obstetric_history' => $medical_record[0]->andrological_gynecological_obstetric_history,
                'medical_history' => $medical_record[0]->medical_history,
                'psychiatric_mental_status' => $medical_record[0]->psychiatric_mental_status,
                'nervous_system' => $medical_record[0]->nervous_system,
                'respiratory_system' => $medical_record[0]->respiratory_system,
                'cardiovascular_system' => $medical_record[0]->cardiovascular_system,
                'digestive_system' => $medical_record[0]->digestive_system,
                'genitourinary_system' => $medical_record[0]->genitourinary_system,
                'musculoskeletal_system' => $medical_record[0]->musculoskeletal_system,
                'endocrine_system' => $medical_record[0]->endocrine_system,
                'sensory_system' => $medical_record[0]->sensory_system,
                'integumentary_system' => $medical_record[0]->integumentary_system,
                'weight' => $medical_record[0]->weight,
                'height' => $medical_record[0]->height,
                'head_circumference' => $medical_record[0]->head_circumference,
                'heart_rate' => $medical_record[0]->heart_rate,
                'respiratory_rate' => $medical_record[0]->respiratory_rate,
                "initial_weight" => $medical_record[0]->initial_weight,
                "weight_age" => $medical_record[0]->weight_age,
                "height_age" => $medical_record[0]->height_age,
                "weight_height" => $medical_record[0]->weight_height,
                "waist_cm" => $medical_record[0]->waist_cm,
                "hip_cm" => $medical_record[0]->hip_cm,
                "chest_cm" => $medical_record[0]->chest_cm,
                "brain_perimeter_cm" => $medical_record[0]->brain_perimeter_cm,
                "brachial_circumference_cm" => $medical_record[0]->brachial_circumference_cm,
                "wrist_circumference_cm" => $medical_record[0]->wrist_circumference_cm,
                "calf_circumference_cm" => $medical_record[0]->calf_circumference_cm,
                "other" => $medical_record[0]->other,
                "imc" => $medical_record[0]->imc,
                'temperature' => $medical_record[0]->temperature,
                'general_inspection' => $medical_record[0]->general_inspection,
                'head' => $medical_record[0]->head,
                'mental_status' => $medical_record[0]->mental_status,
                'hair' => $medical_record[0]->hair,
                'neck' => $medical_record[0]->neck,
                'thorax' => $medical_record[0]->thorax,
                'abdomen' => $medical_record[0]->abdomen,
                'genitalia' => $medical_record[0]->genitalia,
                'anorectal' => $medical_record[0]->anorectal,
                'spine' => $medical_record[0]->spine,
                'upper_lower_limbs' => $medical_record[0]->upper_lower_limbs,
                'peripheral_vascular_system' => $medical_record[0]->peripheral_vascular_system,
                'skin_appendages' => $medical_record[0]->skin_appendages,
                'areas_dryness_excessive_sweating' => $medical_record[0]->areas_dryness_excessive_sweating,
                'diagnostic_impression' => $medical_record[0]->diagnostic_impression,
                'treatment' => $medical_record[0]->treatment,
                'case_analysis' => $medical_record[0]->case_analysis,
                'created_at' => $medical_record[0]->created_at,
                'updated_at' => $medical_record[0]->updated_at,
                'status' => $medical_record[0]->status,
                'subjective' => $medical_record[0]->subjective,
                'objective' => $medical_record[0]->objective,
                'assessment' => $medical_record[0]->assessment,
                'plan' => $medical_record[0]->plan,
                'appointment_id' => $medical_record[0]->appointment_id,
                'type_id' => $medical_record[0]->type_id,
                'appointment_type' => $medical_record[0]->appointment_type
            ];
        }

        return response()->json(['data'=>$data], 200);
    }

    public function internMedicaments(int $event_id, Request $request)
    {
        $Appointment = Appointment::with(['evaluator','candidate'])->where('id', $event_id)->get();
        $candidate = $Appointment[0]->candidate_id;
        $data1 = DB::table('medications')
            ->whereIn('candidate_id', [$candidate])
            ->selectRaw("id,name, dose, frequency, duration, observations,status, created_at, updated_at")
            ->get();
        $data = [];
        foreach ($data1 as $item) {
            $data[] = [
            'id'           => $item->id,
            'name'         => $item->name,
            'dose'         => $item->dose,
            'frequency'    => $item->frequency,
            'duration'     => $item->duration,
            'observations' => $item->observations,
            'status'       => $item->status,
            'created_at'   => $item->created_at,
            'updated_at'   => $item->updated_at,
            ];
        }
        return response()->json(compact('data'), 200);
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
