<?php

namespace Database\Factories;

use App\Enums\CandidateStatus;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Candidate>
 */
class CandidateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthDate = now()->subYears(fake()->numberBetween(10, 15)) ;

        return [
            'first_name'        => fake()->firstName(),
            'middle_name'       => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'birth_date'        => $birthDate,
            'diagnosis'         => fake()->sentence(),
            'sheet'             => fake()->unique()->randomNumber(5), // Genera un número de hoja único
            'info_channel'      => fake()->randomElement(['Redes Sociales', 'Referencia', 'Publicidad', 'Otro']),
            'status'            => fake()->randomElement(['pendiente', 'aceptado', 'rechazado']),
            'admission_comment' => fake()->optional()->sentence(),
            'entry_date'        => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'program_id'        => Program::factory(), // Asocia un programa existente o crea uno nuevo
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }

    /**
     * Indicate that the candidate has been accepted.
     *
     * @return $this
     */
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status'              => CandidateStatus::ACCEPTED,
                'admission_comment'   => null,
                'entry_date'          => fake()->randomElement([fake()->dateTimeBetween('-1 year', 'now'), null]),
            ];
        });
    }

    /**
     * Indicate that the candidate has been rejected.
     *
     * @return $this
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status'              => CandidateStatus::REJECTED,
                'admission_comment'   => fake()->sentence(),
                'entry_date'          => null,
                'program_id'          => null,
                'entry_date'          => fake()->randomElement([fake()->dateTimeBetween('-1 year', 'now'), null]),
            ];
        });
    }

    /**
     * Indicate that the candidate is pending acceptance.
     *
     * @return $this
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status'              => CandidateStatus::PENDING,
                'admission_comment'   => null,
                'entry_date'          => null,
            ];
        });
    }
}
