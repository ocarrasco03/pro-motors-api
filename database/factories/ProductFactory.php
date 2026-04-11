<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sku' => strtoupper($this->faker->bothify('??-####')),
            'brand_id' => Brand::factory(),
            'description' => $this->faker->sentence(6),
            'alternative_sku' => $this->faker->optional()->bothify('??-####'),
            'application' => $this->faker->optional()->sentence(),
            'ean' => $this->faker->optional()->ean13(),
            'sat_code' => $this->faker->optional()->numerify('######'),
            'attributes' => [
                'weight' => $this->faker->randomFloat(2, 0.1, 100),
                'dimensions' => [
                    'length' => $this->faker->randomFloat(2, 1, 100),
                    'width' => $this->faker->randomFloat(2, 1, 100),
                    'height' => $this->faker->randomFloat(2, 1, 100),
                ],
            ],
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn ($attributes) => [
            'is_active' => false,
        ]);
    }

    public function withBrand(Brand $brand): static
    {
        return $this->state(fn ($attributes) => [
            'brand_id' => $brand->id,
        ]);
    }
}
