<?php

namespace Tests\Feature;

use App\Mail\DeductibleReceiptRequested;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\ProcurationActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DonationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Excel::fake();
    }

    /**
     * Helper para generar un usuario válido con todos sus campos obligatorios.
     */
    private function createUser(array $overrides = []): User
    {
        $workAreaId = DB::table('work_areas')->value('id')
            ?? DB::table('work_areas')->insertGetId([
                'name'       => 'Área de Prueba',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return User::create(array_merge([
            'name'             => 'Usuario',
            'last_name'        => 'Prueba',
            'second_last_name' => 'Demo',
            'email'            => 'user_' . uniqid() . '@enlac.org',
            'password'         => Hash::make('password'),
            'work_area_id'     => $workAreaId,
        ], $overrides));
    }

    /**
     * Helper para crear actividades de procuración con tipo obligatorio.
     */
    private function createActivity(array $overrides = []): ProcurationActivity
    {
        return ProcurationActivity::create(array_merge([
            'name' => 'Evento de Prueba ' . uniqid(),
            'type' => 'boteo',
        ], $overrides));
    }

    /**
     * Helper para crear donantes con campos obligatorios de la tabla.
     */
    private function createDonor(array $overrides = []): Donor
    {
        return Donor::create(array_merge([
            'first_name'           => 'Donante',
            'last_name'            => 'Prueba',
            'second_last_name'     => 'Ejemplo',
            'cellphone'            => '5551234567',
            'sector'               => 'Particular',
            'contact_restrictions' => 'ninguna',
        ], $overrides));
    }

    public function test_crea_donativo_genera_folio_y_notifica_a_tesoreria_si_requiere_recibo()
    {
        $role = Role::firstOrCreate(
            ['name' => 'tesoreria', 'guard_name' => 'sanctum'],
            ['label' => 'Tesorería']
        );

        $userTesoreria = $this->createUser([
            'email' => 'tesoreria_test@enlac.org',
        ]);
        $userTesoreria->assignRole($role);

        $activity = $this->createActivity();
        $donor = $this->createDonor();

        $this->actingAs($userTesoreria, 'sanctum');

        $payload = [
            'procuration_activity_id' => $activity->id,
            'donor_id'                => $donor->id,
            'activity_type'           => 'boteo',
            'payment_method'          => 'Efectivo',
            'currency'                => 'MXN',
            'amount'                  => 1250.50,
            'payment_date'            => now()->format('Y-m-d'),
            'has_tax_receipt'         => true,
            'tax_receipt_number'      => 'REC-12345',
        ];

        $response = $this->postJson('/api/donations', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Donativo aplicado con éxito');

        $this->assertDatabaseHas('donations', [
            'donor_id' => $donor->id,
            'amount'   => 1250.50,
        ]);

        Mail::assertSent(DeductibleReceiptRequested::class, function ($mail) use ($userTesoreria) {
            return $mail->hasTo($userTesoreria->email);
        });
    }

    public function test_permite_cancelar_un_donativo_existente()
    {
        $user = $this->createUser();
        $activity = $this->createActivity();

        $donation = Donation::create([
            'procuration_activity_id' => $activity->id,
            'activity_type'           => 'boteo',
            'payment_method'          => 'Efectivo',
            'folio_number'            => 'P-26-99999',
            'amount'                  => 500,
            'payment_date'            => now()->format('Y-m-d'),
            'cancelled_at'            => null,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson("/api/donations/{$donation->id}/cancel");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Donativo cancelado correctamente.');

        $this->assertDatabaseHas('donations', [
            'id'                   => $donation->id,
            'cancelled_by_user_id' => $user->id,
        ]);
    }

    public function test_valida_exportacion_de_reportes_excel()
    {
        $user = $this->createUser();
        $activity = $this->createActivity();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson("/api/donations/export?category=boteo&procuration_activity_id={$activity->id}");

        $response->assertStatus(200);
    }
}
