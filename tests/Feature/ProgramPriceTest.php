<?php

use App\Models\Program;
use App\Models\ProgramPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    // Fijamos una fecha base para controlar el flujo exacto del tiempo en los tests
    //$this->travelTo(Carbon::parse('2026-06-24 08:00:00'));

    $this->program = Program::factory()->create(['price' => 100.00]);
    $user = User::factory()->create();
    $this->actingAs($user);
});

// =========================================================================
// TESTS DEL CONTROLADOR (POST /programs/{program}/prices)
// =========================================================================

test('crea y aplica inmediatamente si la fecha es para el día de hoy sin pendientes', function () {
    $response = $this->postJson(route('program_prices.store'), [
        'program_id'  => $this->program->id,
        'price'       => 150.00,
        'valid_since' => now()->format('Y-m-d')
    ]);

    $response->assertStatus(201);

    // El programa maestro cambió su precio
    expect($this->program->fresh()->price)->toEqual(150.00);

    // Se generó el registro SCD marcado como aplicado
    $this->assertDatabaseHas('program_prices', [
        'program_id'  => $this->program->id,
        'price'       => 150.00,
        'valid_since' => now()->format('Y-m-d'),
        'applied'     => 1,
    ]);
});

test('si se actualiza para hoy y ya existía una pendiente futura, se sobrescribe, se trae a hoy y se aplica', function () {
    // Escenario: Había una tarifa planeada para julio
    $futurePrice = ProgramPrice::factory()->create([
        'program_id'  => $this->program->id,
        'price'       => 200.00,
        'valid_since' => now()->addMonth(),
        'applied'     => 0,
    ]);

    // Acción: Desde la UI deciden mandar una actualización para HOY
    $this->postJson(route('program_prices.store'), [
        'program_id'  => $this->program->id,
        'price'       => 175.00,
        'valid_since' => now(),
    ]);

    // Verificaciones
    expect($this->program->fresh()->price)->toEqual(175.00);

    // El registro pendiente original fue mutado y aplicado a hoy (no quedan pendientes)
    expect(ProgramPrice::where('applied', 0)->count())->toBe(0);
    expect($futurePrice->fresh())->toMatchArray([
        'price'       => 175.00,
        'valid_since' => now()->format('Y-m-d'),
        'applied'     => 1,
    ]);
});

test('crea un registro pendiente si la fecha es futura y no hay otros pendientes', function () {
    $targetDate = now()->addMonth()->format('Y-m-d');
    $startCount = $this->program->prices()->count();

    $this->postJson(route('program_prices.store'), [
        'program_id'  => $this->program->id,
        'price'       => 200.00,
        'valid_since' => $targetDate,
    ]);

    // El precio actual del programa NO debe cambiar
    expect( $this->program->prices()->count() )->toEqual( $startCount + 1 );
    expect($this->program->fresh()->price)->toEqual(100.00);

    // Existe el registro SCD pero no está aplicado
    $this->assertDatabaseHas('program_prices', [
        'program_id'  => $this->program->id,
        'price'       => 200.00,
        'valid_since' => $targetDate,
        'applied'     => 0,
    ]);
});

test('sobrescribe la actualización pendiente existente si se envía otra fecha futura', function () {
    // Primera programación futura (para el 30 de junio)
    $pending = ProgramPrice::factory()->create([
        'program_id' => $this->program->id,
        'price' => 200.00,
        'valid_since' => now()->format('Y-m-d'),
        'applied' => 0,
    ]);

    // Segunda programación (cambio de planes: ahora para el 5 de julio con otro precio)
    $this->postJson(route('program_prices.store'), [
        'program_id'  => $this->program->id,
        'price'       => 250.00,
        'valid_since' => now()->addMonth()->format('Y-m-d'),
    ]);

    // Solo debe seguir existiendo UN registro pendiente en total
    expect(ProgramPrice::where('program_id', $this->program->id)->where('applied', 0)->count())->toBe(1);

    // El registro original mutó sus datos
    expect($pending->fresh())->toMatchArray([
        'price' => 250.00,
        'valid_since' => now()->addMonth()->format('Y-m-d'),
    ]);
});

/* test('no permite registrar precios con fechas pasadas', function () {
    $response = $this->postJson(route('programs.prices.store', $this->program), [
        'price' => 90.00,
        'valid_since' => '2026-06-23', // Ayer
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['valid_since']);
}); */


// =========================================================================
// TESTS DE LA CRONJOB (COMMAND)
// =========================================================================

test('la cronjob aplica el precio correcto cuando llega la fecha futura especificada', function () {
    $futurePrice = ProgramPrice::factory()->create([
        'program_id'  => $this->program->id,
        'price'       => 300.00,
        'valid_since' => now()->addMonth()->format('Y-m-d'),
        'applied'     => 0,
    ]);

    $this->artisan('programs:update-prices')
    ->expectsOutput('No hay cambios de precios programados para hoy.')
    ->assertSuccessful();

    expect($this->program->fresh()->price)->toEqual(100.00);
    expect($futurePrice->fresh()->applied)->toBe(0);

    $newDate = Carbon::parse(now()->addMonth())->format('Y-m-d');
    $this->travelTo( $newDate );

    $this->assertDatabaseHas('program_prices', [
        'applied'     => 0,
        'program_id'  => 1,
        'valid_since' => $newDate
    ]);

    $this->artisan('programs:update-prices')
    ->expectsOutput('Se aplicaron 1 cambios de precio.')
    ->assertSuccessful();

    expect($this->program->fresh()->price)->toEqual(300.00);
    expect($futurePrice->fresh()->applied)->toBe(1);
});
