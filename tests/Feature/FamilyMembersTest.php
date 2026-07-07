<?php

use App\Models\Candidate;
use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// Usar el trait para resetear la base de datos en cada prueba
uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function(){
    $user = User::factory()->create();
    $this->actingAs($user);
});

test('it can list family members', function () {
    $familyMember = FamilyMember::factory()->create();

    $this->getJson('/api/family_members')
        ->assertStatus(200)
        ->assertJsonFragment([
            'name' => $familyMember->name,
            'relationship' => $familyMember->relationship,
        ]);
});

test('it can create a family member', function () {
    $candidate = Candidate::factory()->create();

    $data = [
        'candidate_id'         => $candidate->id,
        'name'                 => 'John Doe',
        'age'                  => 35,
        'relationship'         => 'Padre',
        'marital_status'       => 'Casado',
        'scolarship'           => 'Universitario',
        'ocupation'            => 'Ingeniero',
        'monthly_income'       => 1500.50,
        'monthly_contribution' => 500.00,
    ];

    $this->postJson('/api/family_members', $data)
        ->assertStatus(201);

    $this->assertDatabaseHas('family_members', $data);
});

test('it can show a specific family member', function () {
    $familyMember = FamilyMember::factory()->create();

    $this->getJson("/api/family_members/{$familyMember->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $familyMember->id)
        ->assertJsonPath('data.name', $familyMember->name);
});

test('it can update a family member', function () {
    $familyMember = FamilyMember::factory()->create();

    $data = [
        'name'                 => 'John Doe Updated',
        'age'                  => 36,
        'relationship'         => 'Abuelo',
        'marital_status'       => 'Casado',
        'scolarship'           => 'Universitario',
        'ocupation'            => 'Ingeniero',
        'monthly_income'       => 1600.50,
        'monthly_contribution' => 600.00,
    ];

    $this->putJson("/api/family_members/{$familyMember->id}", $data)
        ->assertStatus(200);

    $this->assertDatabaseHas('family_members', array_merge(['id' => $familyMember->id], $data));
});

test('it can delete a family member', function () {
    $familyMember = FamilyMember::factory()->create();

    $this->deleteJson("/api/family_members/{$familyMember->id}")
        ->assertStatus(204); // Cambia a 200 si tu controlador genérico responde con contenido

    $this->assertDatabaseMissing('family_members', ['id' => $familyMember->id]);
});

test('it validates required fields on creation', function () {
    $this->postJson('/api/family_members', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['candidate_id', 'name']);
});
