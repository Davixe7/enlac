<?php

namespace Database\Factories;

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
        $birthDate = $this->faker->date();

        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $birthDate,
            'diagnosis' => $this->faker->sentence(),
            'sheet' => $this->faker->unique()->randomNumber(5), // Genera un número de hoja único
            'info_channel' => $this->faker->randomElement(['Redes Sociales', 'Referencia', 'Publicidad', 'Otro']),
            'acceptance_status' => $this->faker->randomElement([null, true, false]),
            'rejection_comment' => $this->faker->optional()->sentence(),
            'onboard_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'program_id' => 1, // Asocia un programa existente o crea uno nuevo
            'created_at' => now(),
            'updated_at' => now(),
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
                'acceptance_status' => true,
                'rejection_comment' => null,
                'onboard_at' => $this->faker->randomElement([$this->faker->dateTimeBetween('-1 year', 'now'), null]),
                //'onboard_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
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
                'acceptance_status' => false,
                'rejection_comment' => $this->faker->sentence(),
                'onboard_at' => null,
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
                'acceptance_status' => null,
                'rejection_comment' => null,
                'onboard_at' => null,
            ];
        });
    }
}
