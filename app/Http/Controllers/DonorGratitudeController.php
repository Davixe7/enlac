<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonorGratitudeRequest;
use App\Models\DonorGratitude;
use Illuminate\Http\JsonResponse;

class DonorGratitudeController extends Controller
{
    public function store(StoreDonorGratitudeRequest $request): JsonResponse
    {
        $donor_gratitude = DonorGratitude::create($request->validated());
        return response()->json(['message' => 'Gratitude registered', 'data' => $donor_gratitude], 201);
    }

    public function update(StoreDonorGratitudeRequest $request, DonorGratitude $donor_gratitude): JsonResponse
    {
        $donor_gratitude->update($request->validated());
        return response()->json(['message' => 'Gratitude updated', 'data' => $donor_gratitude]);
    }

    public function destroy(DonorGratitude $donor_gratitude): JsonResponse
    {
        $donor_gratitude->delete();
        return response()->json(['message' => 'Gratitude deleted']);
    }
}
