<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CandidateKardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Candidate $candidate, Request $request)
    {
        $collectionName = 'kardex_' . $request->collection_name;
        $candidate->clearMediaCollection($collectionName);

        if( !$request->filled('detail') ){
            $candidate
            ->addMediaFromRequest('upload')
            ->toMediaCollection($collectionName);
        } else {
            $candidate
            ->addMediaFromDisk('template.pdf')
            ->preservingOriginal()
            ->withCustomProperties(['detail'=>$request->detail])
            ->toMediaCollection($collectionName);
        }
        
        
        $media = $candidate->getFirstMedia($collectionName);
        return new MediaResource($media);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidate $candidate)
    {
        $media = $candidate->media()
        ->where('collection_name', 'like', "kardex_%")
        ->get();

        $media = $media->map(fn($m) => new MediaResource($m))
        ->groupBy('collection_name')
        ->map(fn($m)=>$m[0]);

        return response()->json(['data'=>$media]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidate $candidate, Request $request)
    {
        $candidate->clearMediaCollection('kardex_' . $request->collection_name);
        return response()->json([], 204);
    }
}
