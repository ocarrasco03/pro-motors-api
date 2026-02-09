<?php

namespace Database\Factories;

use App\Core\Enums\StatusEnum;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'owner_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'country' => $this->faker->country(),
            'zip_code' => $this->faker->postcode(),
            'license' => $this->faker->randomElement(['individual', 'corporate']),
            'status' => $this->faker->randomElement(['active', 'inactive', 'suspended']),
            'billing_period' => $this->faker->randomElement(['monthly', 'bimonthly', 'quarterly', 'annual', 'biannual']),
            'slug' => $this->faker->unique()->slug(),
            'created_by' => 'system',
            'updated_by' => 'system',
        ];
    }

    public function withTax(): static
    {
        $tax = Tax::factory()->create();

        return $this->state(fn ($attributes) => [
            'tax_id' => $tax->id,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn ($attributes) => [
            'status' => StatusEnum::ACTIVE,
        ]);
    }
}
