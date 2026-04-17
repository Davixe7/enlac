<?php

namespace App\Http\Controllers;

use App\Models\PlanType;
use Illuminate\Http\Request;

class PlanTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->plan_category_id ){
            $data = PlanType::where('plan_category_id', $request->plan_category_id)->get();
            return response()->json(compact('data'));
        }

        $data = PlanType::with('plan_category')->get();
        return response()->json(compact('data'));
    }

}
