<?php

namespace App\Services;

use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Models\Interview;
use App\Models\Interviewee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InterviewService
{
    public function createInterview(StoreInterviewRequest $request)
    {
        // Iniciar transacción para asegurar la integridad de los datos
        return DB::transaction(function () use ($request) {
            // 1. Crear Entrevistado
            if( $request->filled('interviewee') ){
                $interviewee = Interviewee::updateOrCreate(['candidate_id' => $request->candidate_id], $request->interviewee);
            }

            // 2. Crear Entrevista
            $data = $request->validated();
            $data['interviewee_id'] = $interviewee->id;
            unset($data['answers']);
            unset($data['interviewee']);

            $interview = Interview::create($data);

            // 3. Asociar Respuestas
            if( $request->filled('answers') ){
                $interview->interview_questions()->sync($request->answers);
            }

            return $interview;
        });
    }

    public function updateInterview(UpdateInterviewRequest $request, Interview $interview)
    {
        return DB::transaction(function () use ($interview, $request) {

            if( $request->filled('interviewee') ){
                Interviewee::updateOrCreate(['candidate_id' => $interview->candidate_id],$request->interviewee);
            }

            $data = $request->validated();
            unset($data['answers']);
            unset($data['interviewee']);
            unset($data['id']);

            $data['signed_at'] = !$interview->signed_at && $request->signed_at ? now() : null;
            $interview->update($data);

            if( $request->filled('answers') ){
                $interview->interview_questions()->sync($request->answers);
            }

            return $interview;
        });
    }
}
