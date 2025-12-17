<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Card>
 */
class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unique_name' => fake()->unique()->userName(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'address' => fake()->optional()->address(),
            'phone' => fake()->optional()->phoneNumber(),
            'email_work' => fake()->optional()->email(),
            'email_personal' => fake()->optional()->email(),
            'email_extra1' => fake()->optional()->email(),
            'email_extra2' => fake()->optional()->email(),
            'email_extra3' => fake()->optional()->email(),
            'birthday' => fake()->optional()->date(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the card should have a birthday.
     */
    public function withBirthday(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthday' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the card should not have a birthday.
     */
    public function withoutBirthday(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthday' => null,
        ]);
    }
}
