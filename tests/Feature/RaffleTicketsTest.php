<?php

use App\Models\ProcurationActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function(){
    $this->raffle = ProcurationActivity::create([
        'name'          => 'Obsequio entre amigos, Junio 2026',
        'type'          => 'Obsequio entre Amigos',
        'place'         => 'Albuquerque',
        'tickets_count' => 200,
        'ticket_price'  => 5000,
        'event_date'    => now()->endOfMonth()->format('Y-m-d')
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);
});

test('se crean los 200 tickets de una rifa', function () {

});
