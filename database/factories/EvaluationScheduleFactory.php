<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationSchedule>
 */
class EvaluationScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "date" => $this->faker->date(),
            "evaluator_id" => 1,
            "candidate_id" => 1,
            "status" => 'pending'
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                "date" => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            ];
        });
    }

    public function past()
    {
        return $this->state(function (array $attributes) {
            return [
                "date" => $this->faker->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }
}
