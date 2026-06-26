<?php

use App\Models\Candidate;
use App\Models\ParentQuotaUpdate;
use App\Models\PaymentConfig;
use App\Models\Program;
use App\Models\Sponsorship;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function(){
    $user = User::factory()->create();
    $this->actingAs($user);
});

test('Se crea una actualización para cuota de padres', function () {
    $response = $this->postJson(route('parent-quota-updates.store'), [
        'amount'      => 100.00,
        'valid_since' => now()->addMonth()->format('Y-m-d')
    ]);

    $response->assertStatus(201);
});

test('Se actualiza el incremento de cuota de padres si ya existe una pendiente', function () {
    $originalFutureDate = now()->addMonth()->format('Y-m-d');
    $updatedDate        = Carbon::parse($originalFutureDate)->addDays(15)->format('Y-m-d');

    $firstPending = ParentQuotaUpdate::create([
        'amount' => 100,
        'valid_since' => $originalFutureDate
    ]);

    $response = $this->postJson(route('parent-quota-updates.store'), [
        'amount'      => 150.00,
        'valid_since' => $updatedDate
    ]);

    $response->assertStatus(201);
    expect($firstPending->fresh()->amount)->toBe(150);
    expect($firstPending->fresh()->valid_since)->toBe($updatedDate);
    expect(ParentQuotaUpdate::count())->toBe(1);
});

test('Se incrementa la cuota de padres en el monto indicado y se re-programa', function () {
    $newIncrement = ParentQuotaUpdate::create([
        'amount'      => 100,
        'valid_since' => now()->addYear()->format('Y-m-d')
    ]);

    $parents = Sponsorship::factory(10)
    ->parent()
    ->for(Candidate::factory()->for(Program::factory()))
    ->has(PaymentConfig::factory()->state(fn($atts, Sponsorship $s)=>[
        'candidate_id'    => $s->candidate_id,
        'amount'          => $s->amount,
        'frequency'       => $s->frequency,
        'effective_since' => now()->subYear()
    ]))
    ->create();

    $sumIncrement       = PaymentConfig::whereType('parent')->count() * 100;
    $sumBeforeIncrement = PaymentConfig::whereType('parent')->sum('amount');
    $sumAfterIncrement  = $sumBeforeIncrement + $sumIncrement;

    $this->artisan('sponsorships:apply-increase')->assertSuccessful();
    expect( ParentQuotaUpdate::whereApplied(0)->count() )->toBe(1);
    expect( PaymentConfig::whereType('parent')->sum('amount') )->toBe( $sumAfterIncrement );
});
