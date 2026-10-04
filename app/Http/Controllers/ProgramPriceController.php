<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProgramPriceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->pending) {
            $data = ProgramPrice::whereProgramId($request->program_id)
                ->where('applied', 0)
                ->where('valid_since', '>', now()->format('Y-m-d'))
                ->first();
            return response()->json(compact('data'));
        }

        $data = ProgramPrice::whereProgramId($request->program_id)->get();
        return response()->json(compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id'  => 'required|exists:programs,id',
            'price'       => 'required|numeric',
            'valid_since' => 'required|date'
        ]);

        // Extraemos únicamente la fecha Y-m-d limpia (sin componente de hora)
        $validSinceDate = Carbon::parse($request->input('valid_since'))->format('Y-m-d');
        $todayDate      = now()->format('Y-m-d');

        // Si la fecha enviada es menor o igual al día de hoy, SE APLICA HOY
        $isCurrentOrPast = ($validSinceDate <= $todayDate);
        $validUntil      = Carbon::parse($validSinceDate)->subDay()->format('Y-m-d');

        if ($isCurrentOrPast) {
            // 1. Cerramos la vigencia del registro activo anterior
            ProgramPrice::where('program_id', $request->program_id)
                ->where('applied', 1)
                ->whereNull('valid_until')
                ->update(['valid_until' => $validUntil]);

            // 2. Eliminamos o limpiamos cualquier registro futuro pendiente
            ProgramPrice::where('program_id', $request->program_id)
                ->where('applied', 0)
                ->delete();

            // 3. Insertamos el nuevo precio activo (applied = 1)
            $priceRecord = ProgramPrice::create([
                'program_id'  => $request->program_id,
                'price'       => $request->price,
                'valid_since' => $validSinceDate,
                'valid_until' => null,
                'applied'     => 1
            ]);

            // 4. Actualizamos de forma inmediata la columna price en la tabla programs
            $program = Program::find($request->program_id);
            $program->update(['price' => $request->price]);

        } else {
            // Es una fecha estrictamente futura
            $priceRecord = ProgramPrice::updateOrCreate(
                [
                    'program_id' => $request->program_id,
                    'applied'    => 0
                ],
                [
                    'price'       => $request->price,
                    'valid_since' => $validSinceDate,
                    'valid_until' => null,
                    'applied'     => 0
                ]
            );

            $program = Program::find($request->program_id);
        }

        return response()->json([
            'data'    => $priceRecord,
            'program' => $program->fresh()
        ], 201);
    }

    public function update(Request $request, ProgramPrice $programPrice)
    {
        $validated = $request->validate([
            'price'       => 'sometimes|numeric',
            'valid_since' => 'sometimes|date'
        ]);

        $validSinceDate = Carbon::parse($request->input('valid_since', $programPrice->valid_since))->format('Y-m-d');
        $todayDate      = now()->format('Y-m-d');

        $isCurrentOrPast = ($validSinceDate <= $todayDate);
        $validUntil      = Carbon::parse($validSinceDate)->subDay()->format('Y-m-d');

        if ($isCurrentOrPast) {
            ProgramPrice::where('program_id', $programPrice->program_id)
                ->where('id', '!=', $programPrice->id)
                ->where('applied', 1)
                ->whereNull('valid_until')
                ->update(['valid_until' => $validUntil]);

            Program::where('id', $programPrice->program_id)
                ->update(['price' => $request->input('price', $programPrice->price)]);

            $programPrice->update([
                'price'       => $request->input('price', $programPrice->price),
                'valid_since' => $validSinceDate,
                'valid_until' => null,
                'applied'     => 1
            ]);
        } else {
            $programPrice->update([
                'price'       => $request->input('price', $programPrice->price),
                'valid_since' => $validSinceDate,
                'applied'     => 0
            ]);
        }

        $program = Program::find($programPrice->program_id);

        return response()->json([
            'data'    => $programPrice,
            'program' => $program->fresh()
        ], 200);
    }

    public function destroy(ProgramPrice $programPrice)
    {
        //
    }
}
