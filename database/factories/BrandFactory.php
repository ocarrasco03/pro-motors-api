<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'slug' => $this->faker->unique()->slug(2),
            'logo_url' => $this->faker->optional()->imageUrl(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn ($attributes) => [
            'is_active' => false,
        ]);
    }
}
