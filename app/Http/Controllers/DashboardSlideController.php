<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardSlideResource;
use App\Models\DashboardSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardSlideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DashboardSlide::where('enabled', 1)->orderBy('order')->get();
        return DashboardSlideResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'   => 'required',
            'picture' => 'required|file',
        ]);

        $latestIndex = DashboardSlide::orderBy('order')->limit(1)->pluck('order')->first() ?: 1;

        $data = DashboardSlide::create([
            'title' => $request->title,
            'order' => $request->order ?: $latestIndex,
            'enabled' => true,
        ]);

        $data->addMediaFromRequest('picture')->toMediaCollection('picture');

        return response()->json(compact('data'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function reorder(Request $request)
    {
        $data = $request->validate(['id_order' => 'required|array']);
        foreach( $data['id_order'] as $slide ){
            DB::table('dashboard_slides')->where('id', $slide['id'])->update(['order'=>$slide['order']]);
        }
        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DashboardSlide $dashboardSlide)
    {
        $dashboardSlide->delete();
        return response()->json([], 200);
    }
}
