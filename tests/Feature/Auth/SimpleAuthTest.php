<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\Tax;
use App\Models\User;
use Database\Seeders\BootRolesPermissionsSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_authentication_works()
    {
        $this->seed(BootRolesPermissionsSeeder::class);

        // Create tax record first
        $tax = Tax::create([
            'name' => 'Test Tax',
            'type' => 'iva',
            'rate' => 0.16,
        ]);

        // Create company
        $company = Company::create([
            'name' => 'Test Company',
            'slug' => 'test-company',
            'email' => 'test@company.com',
            'owner_name' => 'Test Owner',
            'tax_id' => $tax->id,
            'created_by' => 'system',
            'updated_by' => 'system',
        ]);

        // Create user
        $user = User::factory()->create([
            'company_id' => $company->id,
            'username' => 'testuser',
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('user');

        // Test login
        $response = $this->postJson('/api/v1/auth/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                    'expires_at',
                ],
            ]);
    }
}
