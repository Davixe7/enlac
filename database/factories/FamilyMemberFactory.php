<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Crea un Candidate automáticamente si no se le pasa uno explícitamente
            'candidate_id'         => Candidate::factory(),

            'name'                 => fake()->firstName(),

            // Usamos el helper global fake()
            'age'                  => fake()->optional()->numberBetween(0, 100),

            'relationship'         => fake()->optional()->randomElement(['Padre', 'Madre', 'Hermano/a', 'Hijo/a', 'Cónyuge']),
            'marital_status'       => fake()->optional()->randomElement(['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión Libre']),
            'scolarship'           => fake()->optional()->randomElement(['Ninguna', 'Primaria', 'Secundaria', 'Bachillerato', 'Universitaria', 'Posgrado']),
            'ocupation'            => fake()->optional()->jobTitle(),

            // Decimales simulando montos de dinero
            'monthly_income'       => fake()->randomFloat(2, 0, 10000),
            'monthly_contribution' => fake()->randomFloat(2, 0, 5000),
        ];
    }
}
