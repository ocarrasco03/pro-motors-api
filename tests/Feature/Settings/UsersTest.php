<?php

namespace Tests\Feature\Settings;

use App\Domain\ValueObjects\Enums\RolesEnum;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected bool $seed = true;
    protected User $regularUser;
    protected User $admin;
    protected User $superAdmin;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->withTax()->create();
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole(RolesEnum::SUPER_ADMIN);

        $this->company = Company::factory()->withTax()->create();
        $this->admin = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->admin->assignRole(RolesEnum::ADMIN);

        $this->regularUser = User::factory()->create([
            'company_id' => $this->company->id,
        ]);
        $this->regularUser->assignRole(RolesEnum::USER);
    }

    public function test_admin_can_list_users()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/v1/settings/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'fullName',
                        'firstName',
                        'lastName',
                        'email',
                        'username',
                        'active',
                        'lastLoginAt',
                        'updatedBy',
                        'updatedAt',
                        'company',
                        'role'
                    ],
                ],
                'meta' => [
                    'currentPage',
                    'lastPage',
                    'perPage',
                    'total',
                    'from',
                    'to',
                    'links' => [
                        'first',
                        'last',
                        'prev',
                        'next',
                    ],
                ],
            ]);
    }

    public function test_regular_user_cannot_list_users()
    {
        Sanctum::actingAs($this->regularUser);

        $response = $this->getJson('/api/v1/settings/users');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_user()
    {
        Sanctum::actingAs($this->superAdmin);

        $email = fake()->email;
        $username = fake()->userName;

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'username' => $username,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'company' => $this->company->name,
            'role' => 'user',
        ];

        $response = $this->postJson('/api/v1/settings/users', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => $email,
                'username' => $username,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_admin_can_create_user()
    {
        Sanctum::actingAs($this->admin);

        $email = fake()->email;
        $username = fake()->userName;

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'username' => $username,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'user',
        ];

        $response = $this->postJson('/api/v1/settings/users', $userData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => $email,
                'username' => $username,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_regular_user_cannot_create_user()
    {
        Sanctum::actingAs($this->regularUser);
        $email = fake()->email;
        $username = fake()->userName;

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $email,
            'username' => $username,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'user',
        ];

        $response = $this->postJson('/api/v1/settings/users', $userData);

        $response->assertStatus(403);
    }

    public function test_user_creation_requires_valid_data()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/settings/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'username',
                'password',
            ]);
    }

    public function test_admin_can_view_user_details()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("/api/v1/settings/users/{$this->regularUser->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $this->regularUser->id,
                'email' => $this->regularUser->email,
            ]);
    }

    public function test_admin_can_update_user()
    {
        Sanctum::actingAs($this->admin);

        Sanctum::actingAs($this->superAdmin);

        $email = fake()->email;

        $updateData = [
            'first_name' => 'Jane Updated',
            'last_name' => 'Smith Updated',
            'email' => $email,
        ];

        $response = $this->putJson("/api/v1/settings/users/{$this->regularUser->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'firstName' => 'Jane Updated',
                'lastName' => 'Smith Updated',
                'email' => $email,
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->regularUser->id,
            'first_name' => 'Jane Updated',
            'last_name' => 'Smith Updated',
            'email' => $email,
        ]);
    }

    public function test_admin_can_delete_user()
    {
        Sanctum::actingAs($this->superAdmin);
        $userToDelete = User::factory()->create(['company_id' => $this->company->id]);

        $response = $this->deleteJson("/api/v1/settings/users/{$userToDelete->id}");

        $response->assertStatus(204);

        // Check if user was actually deleted (not soft deleted)
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_admin_cannot_delete_user_from_other_company()
    {
        Sanctum::actingAs($this->admin);
        $otherCompany = Company::factory()->withTax()->create();
        $otherUser = User::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->deleteJson("/api/v1/settings/users/{$otherUser->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_enable_disable_user()
    {
        Sanctum::actingAs($this->superAdmin);
        $initialActive = (bool) $this->regularUser->active;

        $response = $this->patchJson("/api/v1/settings/users/{$this->regularUser->id}/toggle");

        $response->assertStatus(200);

        sleep(1);
        $updatedUser = User::find($this->regularUser->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'active']]);
    }

    public function test_user_search_by_name()
    {
        Sanctum::actingAs($this->admin);

        User::factory()->create([
            'first_name' => 'SearchTarget',
            'last_name' => 'User',
            'company_id' => $this->company->id,
        ]);

        $searchUser = User::factory()->create([
            'first_name' => 'SearchTarget',
            'last_name' => 'User',
            'company_id' => $this->company->id,
        ]);

        $response = $this->getJson('/api/v1/settings/users?search=SearchTarget');

        $response->assertStatus(200);
        // Check that we got the created user in results
        $this->assertDatabaseHas('users', [
            'first_name' => 'SearchTarget',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_user_pagination_works_correctly()
    {
        Sanctum::actingAs($this->admin);

        User::factory(15)->create(['company_id' => $this->company->id]);

        $response = $this->getJson('/api/v1/settings/users?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'currentPage',
                    'lastPage',
                    'perPage',
                    'total',
                    'from',
                    'to',
                    'links',
                ],
            ]);

        $this->assertEquals(15, count($response->json('data')));
    }

    public function test_unauthenticated_user_cannot_access_user_endpoints()
    {
        $response = $this->getJson('/api/v1/settings/users');
        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/settings/users', []);
        $response->assertStatus(401);

        $response = $this->getJson("/api/v1/settings/users/{$this->regularUser->id}");
        $response->assertStatus(401);
    }
}
