<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceListFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug(3),
            'description' => $this->faker->optional()->sentence(),
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function default(): static
    {
        return $this->state(fn ($attributes) => [
            'is_default' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn ($attributes) => [
            'is_active' => false,
        ]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn ($attributes) => [
            'company_id' => $company->id,
        ]);
    }
}
