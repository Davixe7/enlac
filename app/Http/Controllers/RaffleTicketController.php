<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Raffle;
use App\Models\RaffleSeller;
use App\Models\RaffleTicket;
use App\Models\User;
use App\Mail\TicketDeductibleReceiptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RaffleTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RaffleTicket $raffleTicket)
    {
        $data = $raffleTicket->load(['raffle.activity', 'buyer', 'seller']);
        return response()->json(compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RaffleTicket $raffleTicket)
    {
        $data = $request->validate([
            'buyer.first_name'     => 'required|string|max:191',
            'buyer.personal_email' => 'nullable|string|max:191',
            'buyer.cellphone'      => 'required|string|max:191',
            'seller.first_name'    => 'required|string|max:191',
            'seller.phone'         => 'required|string|max:191',
            'cow'                  => 'required|boolean',
            'deductible_receipt'   => 'required|boolean',
            'enlac_collection'     => 'required|boolean',
            'comments'             => 'nullable|string|max:191',
            'sold_at'              => 'required|date'
        ]);

        if( !$raffleTicket->seller_id && $request->filled('seller.phone') ){
            $seller = RaffleSeller::firstOrCreate(['phone' => $request->input('seller.phone')], [
                'first_name' => $request->input('seller.first_name'),
                'last_name'  => ''
            ]);
            $data['raffle_seller_id'] = $seller->id;
        }

        if( !$raffleTicket->donor_id && $request->filled('buyer.cellphone') ){
            $donor = Donor::firstOrCreate(['cellphone' => $request->input('buyer.cellphone')], [
                'first_name' => $request->input('buyer.first_name'),
                'last_name'  => '',
                'sector'     => '',
                'contact_restrictions' => ''
            ]);
            $data['donor_id'] = $donor->id;
        }

        unset($data['buyer']);
        unset($data['seller']);

        // Guardamos si el boleto estaba disponible antes del cambio
        $wasAvailable = $raffleTicket->status === 'available';

        $data['status'] = $wasAvailable ? 'sold' : $raffleTicket->status;

        $raffleTicket->update($data);
        $raffleTicket->load(['buyer', 'seller']);

        // Notificar a Tesorería solo si requiere recibo deducible Y acaba de venderse (o era una venta nueva)
        if ($raffleTicket->deductible_receipt && $wasAvailable) {
            $treasuryUsers = User::role('tesoreria', 'sanctum')->get();

            foreach ($treasuryUsers as $user) {
                if ($user->email) {
                    Mail::to($user->email)->queue(new TicketDeductibleReceiptMail($raffleTicket));
                }
            }
        }

        $data = $raffleTicket;
        return response()->json(compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RaffleTicket $raffleTicket)
    {
        //
    }
}
