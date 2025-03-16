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
            // 1. Crear Interview
            $data = $request->validated();
            unset($data['answers']);
            unset($data['interviewee']);

            $interview = Interview::create($data);

            // 2. Crear Interview Questions
            if( $request->filled('answers') ){
                $interview->interview_questions()->sync($request->answers);
            }

            // 3. Crear Interviewee
            if( $request->filled('interviewee') ){
                Interviewee::create($request->interviewee);
            }

            return $interview;
        });
    }

    public function updateInterview(UpdateInterviewRequest $request, Interview $interview)
    {
        return DB::transaction(function () use ($interview, $request) {
            $interviewData = $request->validated();
            unset($interviewData['answers']);
            unset($interviewData['interviewee']);
            unset($interviewData['id']);

            $data['signed_at'] = !$interview->signed_at && $request->signed_at ? now() : null;
            $interview->update($interviewData);

            if( $request->filled('answers') ){
                $interview->interview_questions()->sync($request->answers);
            }

            if( $request->filled('interviewee') ){
                Interviewee::update($request->interviewee);
            }

            return $interview;
        });
    }
}
