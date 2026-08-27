<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRaffleRequest;
use App\Services\RaffleService;
use App\Models\Raffle;
use App\Models\RaffleSeller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\RaffleTicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if( $request->filled('procuration_activity_id') ){
            $data = Raffle::whereProcurationActivityId( $request->procuration_activity_id )
            ->with(['tickets.buyer', 'tickets.seller', 'tickets' => function($query){
                $query->withSum('donations', 'amount');
            }])->firstOrFail();
            return response()->json(compact('data'));
        }

        $data = Raffle::with(['tickets.buyer', 'tickets.seller']);
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRaffleRequest $request, RaffleService $raffleService)
    {
        $raffle = $raffleService->createOrUpdateRaffle($request->validated());

        return response()->json([
            'data' => $raffle
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Raffle $raffle)
    {
        $data = $raffle->load(['activity', 'tickets.buyer', 'tickets.seller']);
        return response()->json(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Raffle $raffle)
    {
        $data = $request->validate([
            'tickets_count'           => 'nullable|integer|min:0',
            'ticket_price'            => 'nullable|numeric|min:0|between:0,99999999.99',
            'place'                   => 'nullable|string|max:255',
            'winning_ticket'          => 'nullable|string|max:255',
            'winner_name'             => 'nullable|string|max:255',
            'seller_winner_name'      => 'nullable|string|max:255',
        ]);

        $data = $raffle->update($data);

        return response()->json(compact('data'));
    }

    public function assignTickets(Request $request, Raffle $raffle){
        $data = $request->validate([
            'starts_at'          => 'required|numeric|min:1',
            'ends_at'            => 'required|numeric|min:2|max:200',
            'seller.first_name'  => 'required',
            'seller.phone'       => 'required|string|min:10',
        ]);

        $raffleSeller = RaffleSeller::updateOrCreate(['phone'=>$request->input('seller.phone')], [
            'first_name' => $request->input('seller.first_name'),
            'last_name'  => ''
        ]);

        $raffle->tickets()
        ->whereBetween('number', [$request->starts_at, $request->ends_at])
        ->update(['raffle_seller_id' => $raffleSeller->id]);

        return response()->json([], 200);
    }

    public function startsAt(Request $request, Raffle $raffle){
        $ticket = $raffle->tickets()
        ->whereStatus('available')
        ->whereNull('raffle_seller_id')
        ->orderBy('id', 'asc')
        ->first();

        if($ticket){
            return response()->json(['data'=>$ticket->number]);
        }
        return response()->json([], 404);
    }

    public function setWinner(Request $request, Raffle $raffle)
    {
        $request->validate([
            'raffle_ticket_id' => 'required|exists:raffle_tickets,id',
        ]);

        $winningTicket = $raffle->tickets()
            ->with(['buyer', 'seller'])
            ->where('id', $request->raffle_ticket_id)
            ->firstOrFail();

        DB::transaction(function () use ($raffle, $winningTicket) {
            // 1. Marcar todos los boletos de esta rifa como descartados
            $raffle->tickets()->update(['status' => 'discarded']);

            // 2. Marcar el boleto seleccionado como ganador
            $winningTicket->update(['status' => 'won']);

            // 3. Guardar información del ganador en la cabecera de la rifa
            $raffle->update([
                'winning_ticket'     => $winningTicket->number,
                'winner_name'        => $winningTicket->buyer ? $winningTicket->buyer->first_name : 'Sin Comprador',
                'seller_winner_name' => $winningTicket->seller ? $winningTicket->seller->first_name : 'Sin Vendedor',
            ]);
        });

        return response()->json([
            'message' => 'Ganador registrado exitosamente y boletos restantes descartados.',
            'data'    => $raffle->fresh(['tickets.buyer', 'tickets.seller'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Raffle $raffle)
    {
        $raffle->delete();
        return response()->json([], 200);
    }

    public function export(Raffle $raffle)
    {
        $fileName = 'reporte-rifa-' . $raffle->id . '-' . now()->format('d-m-Y') . '.xlsx';
        return Excel::download(new RaffleTicketsExport($raffle), $fileName);
    }

    /**
     * Descarta o declara ganador un boleto individual durante el sorteo en vivo
     */
    public function toggleTicketDiscard(Request $request, Raffle $raffle)
    {
        $request->validate([
            'ticket_id' => 'required|exists:raffle_tickets,id',
            'status'    => 'required|in:discarded,sold,won'
        ]);

        $ticket = $raffle->tickets()->where('id', $request->ticket_id)->firstOrFail();

        DB::transaction(function () use ($raffle, $ticket, $request) {
            $ticket->update(['status' => $request->status]);

            // Si se marca como ganador, registrarlo en la cabecera de la rifa
            if ($request->status === 'won') {
                $ticket->load(['buyer', 'seller']);
                $raffle->update([
                    'winning_ticket'     => $ticket->number,
                    'winner_name'        => $ticket->buyer ? $ticket->buyer->first_name : 'Sin Comprador',
                    'seller_winner_name' => $ticket->seller ? $ticket->seller->first_name : 'Sin Vendedor',
                ]);
            }
        });

        // Refrescar el Cache del Stream
        $this->broadcastStreamState($raffle);

        return response()->json([
            'message' => 'Estatus de boleto actualizado',
            'ticket'  => $ticket
        ]);
    }

    /**
     * Devuelve el estado actual de la rifa para el Stream (Lee de Cache o DB)
     */
    public function getStreamState(Raffle $raffle)
    {
        $cacheKey = "raffle_stream_state_{$raffle->id}";

        $data = Cache::remember($cacheKey, 3600, function () use ($raffle) {
            $raffle->load(['activity', 'tickets.buyer']);

            $soldTickets = $raffle->tickets
                ->whereIn('status', ['sold', 'discarded', 'won'])
                ->values();

            return [
                'raffle_id'      => $raffle->id,
                'winning_ticket' => $raffle->winning_ticket,
                'winner_name'    => $raffle->winner_name,
                'activity_name'  => $raffle->procurationActivity?->name ?? 'Sin Actividad',
                'tickets'        => $soldTickets->map(fn($t) => [
                    'id'         => $t->id,
                    'number'     => $t->number,
                    'status'     => $t->status,
                    'buyer_name' => $t->buyer ? $t->buyer->first_name : 'N/A',
                ]),
                'updated_at'     => now()->toIso8601String()
            ];
        });

        return response()->json($data);
    }

    /**
     * Función auxiliar para actualizar el Cache cuando haya un cambio
     */
    private function broadcastStreamState(Raffle $raffle)
    {
        $cacheKey = "raffle_stream_state_{$raffle->id}";
        Cache::forget($cacheKey);
        $this->getStreamState($raffle);
    }
}
