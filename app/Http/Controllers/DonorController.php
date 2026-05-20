<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Http\Requests\StoreDonorRequest;
use Illuminate\Http\JsonResponse;

class DonorController extends Controller
{
    public function index(): JsonResponse
    {
        // Traemos los donantes ordenados alfabéticamente por defecto
        $donors = Donor::orderBy('first_name')->get()->append('full_name');
        return response()->json($donors);
    }

    public function store(StoreDonorRequest $request): JsonResponse
    {
        $donor = Donor::create($request->validated());
        return response()->json($donor->append('full_name'), 201);
    }
}
