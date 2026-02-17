<?php

namespace App\Http\Controllers;

use App\Enums\CandidateStatus;
use Illuminate\Http\Request;

class CandidateStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = collect(CandidateStatus::cases());

        $data = $data->reject(fn ($status) => in_array($status->value, explode(',', $request->exclude)));
        $data = $data->map(function($status){
            $option = [
                'value'   => $status->value,
                'label'   => $status->label(),
                'disable' => $status->value == CandidateStatus::SCHEDULED->value ? true : false,
            ];
            return $option;
        })->values();

        return response()->json(compact('data'));
    }
}
