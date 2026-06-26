<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sponsorship>
 */
class SponsorshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount'     => fake()->numberBetween(100, 500),
            'frequency'  => 1,
            'sponsor_id' => null,
            'type'       => 'parent',
            'month_payday' => 1,
            'address_type' => 'home'
        ];
    }

    public function parent(){
        return $this->state(fn(array $atts)=>[
            'type' => 'parent'
        ]);
    }

    public function sponsor(){
        return $this->state(fn(array $atts)=>[
            'type' => 'sponsor'
        ]);
    }
}
