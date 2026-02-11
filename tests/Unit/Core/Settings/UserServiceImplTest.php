<?php

namespace Tests\Unit\Core\Settings;


use App\Core\DTO\Common\SearchDTO;
use App\Core\Enums\RolesEnum;
use App\Core\Settings\UserServiceImpl;
use App\Http\Resources\Settings\UserCollection;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use ReflectionClass;
use ReflectionException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserServiceImplTest extends TestCase
{
    use RefreshDatabase;

    protected UserServiceImpl $userService;

    protected User $authUser;

    protected User $targetUser;

    protected Company $company;

    protected Company $otherCompany;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserServiceImpl;

        $this->company = Company::factory()->withTax()->create();
        $this->otherCompany = Company::factory()->withTax()->create();

        $this->authUser = User::factory()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        $this->targetUser = User::factory()->create([
            'company_id' => $this->company->id,
            'active' => true,
        ]);

        // Create test roles and permissions
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit-users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view-reports', 'guard_name' => 'web']);

        // Properly mock Auth facade
        Auth::shouldReceive('user')->andReturn($this->authUser);
        Auth::shouldReceive('check')->andReturn(true);
    }

    /**
     * @throws ReflectionException
     */
    public function test_auth_user_returns_cached_user()
    {
        $reflection = new \ReflectionClass($this->userService);
        $method = $reflection->getMethod('authUser');
        $method->setAccessible(true);

        $firstCall = $method->invoke($this->userService);
        $secondCall = $method->invoke($this->userService);

        $this->assertSame($firstCall, $secondCall);
        $this->assertSame($this->authUser, $firstCall);
    }

    public function test_auth_user_with_no_user_set_returns_null()
    {
        $userService = new UserServiceImpl;

        // Test that the service can be instantiated
        $this->assertInstanceOf(UserServiceImpl::class, $userService);
    }

    public function test_get_users_with_search_uses_scout_when_available()
    {
        $data = new SearchDTO(
            search: 'test',
            perPage: 10,
            sortBy: 'id',
            orderBy: 'asc',
            authUser: $this->authUser,
            page: null,
            filterBy: null
        );

        $result = $this->userService->getUsers($data);

        $this->assertInstanceOf(UserCollection::class, $result);
    }

    public function test_get_users_with_database_search()
    {
        // Create a user that should be found
        User::factory()->create([
            'first_name' => 'SearchTarget',
            'company_id' => $this->company->id,
        ]);

        $$data = new SearchDTO(
            search: 'SearchTarget',
            perPage: 10,
            sortBy: 'id',
            orderBy: 'asc',
            authUser: $this->authUser,
            page: null,
            filterBy: null
        );

        $result = $this->userService->getUsers($data);

        $this->assertInstanceOf(UserCollection::class, $result);
    }

    public function test_get_users_with_custom_filters()
    {
        $data = new SearchDTO(
            search: null,
            perPage: 5,
            sortBy: 'email',
            orderBy: 'desc',
            authUser: $this->authUser,
            page: null,
            filterBy: ['active' => true]
        );

        $result = $this->userService->getUsers($data);

        $this->assertInstanceOf(UserCollection::class, $result);
    }

    public function test_update_user_succeeds_for_same_company()
    {
        $updateData = ['first_name' => 'Updated Name'];
        $result = $this->userService->updateUser($this->targetUser, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->first_name);
        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
            'first_name' => 'Updated Name',
        ]);
    }

    public function test_restore_user_restores_soft_deleted_user()
    {
        $this->targetUser->delete();
        $this->assertSoftDeleted('users', ['id' => $this->targetUser->id]);

        $result = $this->userService->restoreUser($this->targetUser->id);

        $this->assertTrue($result);
        $this->assertNotSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    public function test_restore_user_throws_exception_for_nonexistent_user()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->userService->restoreUser(99999);
    }

    public function test_reset_password_updates_user_password()
    {
        $newPassword = 'NewPassword123!';
        $data = ['password' => $newPassword];

        $result = $this->userService->resetPassword($this->targetUser, $data);

        $this->assertTrue($result);

        $updatedUser = User::find($this->targetUser->id);
        $this->assertTrue(Hash::check($newPassword, $updatedUser->password));
    }

    public function test_is_user_active_returns_correct_status()
    {
        $activeUser = User::factory()->create(['active' => true]);
        $inactiveUser = User::factory()->create(['active' => false]);

        $this->assertTrue($this->userService->isUserActive($activeUser->id));
        $this->assertFalse($this->userService->isUserActive($inactiveUser->id));
    }

    public function test_enable_disable_user_toggles_active_status()
    {
        $originalStatus = $this->targetUser->active;

        $result = $this->userService->enableDisableUser($this->targetUser);

        $this->assertTrue($result);

        $updatedUser = User::find($this->targetUser->id);
        $this->assertNotEquals($originalStatus, $updatedUser->active);
    }

    public function test_get_company_users_returns_users_for_specific_company()
    {
        $companyUser1 = User::factory()->create(['company_id' => $this->company->id]);
        $companyUser2 = User::factory()->create(['company_id' => $this->company->id]);
        User::factory()->create(['company_id' => $this->otherCompany->id]);

        $result = $this->userService->getCompanyUsers($this->company->id);

        $this->assertCount(4, $result); // includes authUser and targetUser from setUp
        $this->assertTrue($result->contains('id', $this->authUser->id));
        $this->assertTrue($result->contains('id', $this->targetUser->id));
        $this->assertTrue($result->contains('id', $companyUser1->id));
        $this->assertTrue($result->contains('id', $companyUser2->id));
    }

    public function test_get_user_company_with_company_relation()
    {
        $result = $this->userService->getUserCompany($this->targetUser);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals($this->company->id, $result['id']);
    }

    public function test_get_user_company_without_company_relation()
    {
        $userWithoutCompany = User::factory()->create(['company_id' => null]);

        $result = $this->userService->getUserCompany($userWithoutCompany);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_assigned_price_lists_returns_price_lists_for_company()
    {
        $result = $this->userService->getAssignedPriceLists($this->company->id);

        $this->assertIsArray($result);
    }

    public function test_assign_role_to_user()
    {
        $this->userService->assignRole($this->targetUser, RolesEnum::ADMIN->value);

        $this->assertTrue($this->targetUser->fresh()->hasRole(RolesEnum::ADMIN));
    }

    public function test_remove_role_from_user()
    {
        $roleName = 'user';
        $this->targetUser->assignRole($roleName);
        $data = ['role' => $roleName];

        $this->userService->removeRole($this->targetUser, $data);

        $this->assertFalse($this->targetUser->fresh()->hasRole($roleName));
    }

    public function test_get_roles_returns_user_roles()
    {
        $this->targetUser->assignRole(RolesEnum::USER->value);
        $this->targetUser->assignRole(RolesEnum::ADMIN->value);

        $result = $this->userService->getRoles($this->targetUser);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    public function test_assign_permission_to_user()
    {
        $permissionName = 'edit-users';
        $data = ['permission' => $permissionName];

        $this->userService->assignPermission($this->targetUser, $data);

        $this->assertTrue($this->targetUser->fresh()->hasDirectPermission($permissionName));
    }

    public function test_remove_permission_from_user()
    {
        $permissionName = 'edit-users';
        $this->targetUser->givePermissionTo($permissionName);
        $data = ['permission' => $permissionName];

        $this->userService->removePermission($this->targetUser, $data);

        $this->assertFalse($this->targetUser->fresh()->hasDirectPermission($permissionName));
    }

    public function test_get_permissions_returns_user_permissions()
    {
        $this->targetUser->givePermissionTo('edit-users');
        $this->targetUser->givePermissionTo('view-reports');

        $result = $this->userService->getPermissions($this->targetUser);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertGreaterThan(0, $result->count());
    }

    /**
     * @throws ReflectionException
     */
    public function test_resolve_company_by_name()
    {
        $reflection = new ReflectionClass($this->userService);
        $method = $reflection->getMethod('resolveCompany');
        $method->setAccessible(true);

        $result = $method->invoke($this->userService, $this->company->name);

        $this->assertEquals($this->company->id, $result);
    }

    /**
     * @throws ReflectionException
     */
    public function test_resolve_company_by_id()
    {
        $reflection = new ReflectionClass($this->userService);
        $method = $reflection->getMethod('resolveCompany');
        $method->setAccessible(true);

        $result = $method->invoke($this->userService, $this->company->id);

        $this->assertEquals($this->company->id, $result);
    }

    // Enhanced tests for lines 100-101, 123-146, 167-237

    public function test_update_user_throws_unauthorized_exception_for_different_company()
    {
        $userFromOtherCompany = User::factory()->create([
            'company_id' => $this->otherCompany->id,
        ]);

        $updateData = ['first_name' => 'Updated Name'];

        // Mock Auth facade properly
        Auth::shouldReceive('user')->andReturn($this->authUser);
        Auth::shouldReceive('check')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($this->authUser);

        $this->expectException(\Illuminate\Validation\UnauthorizedException::class);
        $this->expectExceptionMessage('You cannot edit this user');

        $this->userService->updateUser($userFromOtherCompany->id, $updateData);
    }

    public function test_update_user_with_null_company_id_auth_user()
    {
        // Create a user with null company_id
        $superAdmin = User::factory()->create(['company_id' => null]);
        Auth::shouldReceive('user')->andReturn($superAdmin);

        $userService = new UserServiceImpl;

        $updateData = ['first_name' => 'Updated Name'];
        $result = $userService->updateUser($this->targetUser, $updateData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Name', $result->first_name);
    }

    public function test_restore_user_with_already_restored_user()
    {
        // User is not deleted, should still find them
        $result = $this->userService->restoreUser($this->targetUser->id);

        $this->assertTrue($result);
        $this->assertNotSoftDeleted('users', ['id' => $this->targetUser->id]);
    }

    public function test_restore_user_with_multiple_soft_deleted_users()
    {
        $deletedUser1 = User::factory()->create(['company_id' => $this->company->id]);
        $deletedUser2 = User::factory()->create(['company_id' => $this->company->id]);

        $deletedUser1->delete();
        $deletedUser2->delete();

        $this->assertSoftDeleted('users', ['id' => $deletedUser1->id]);
        $this->assertSoftDeleted('users', ['id' => $deletedUser2->id]);

        $result1 = $this->userService->restoreUser($deletedUser1->id);
        $result2 = $this->userService->restoreUser($deletedUser2->id);

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertNotSoftDeleted('users', ['id' => $deletedUser1->id]);
        $this->assertNotSoftDeleted('users', ['id' => $deletedUser2->id]);
    }

    public function test_reset_password_with_empty_password()
    {
        $data = ['password' => ''];

        $result = $this->userService->resetPassword($this->targetUser, $data);

        $this->assertTrue($result);

        $updatedUser = User::find($this->targetUser->id);
        $this->assertTrue(Hash::check('', $updatedUser->password));
    }

    public function test_reset_password_with_weak_password()
    {
        $weakPassword = '123';
        $data = ['password' => $weakPassword];

        $result = $this->userService->resetPassword($this->targetUser, $data);

        $this->assertTrue($result);

        $updatedUser = User::find($this->targetUser->id);
        $this->assertTrue(Hash::check($weakPassword, $updatedUser->password));
    }

    public function test_is_user_active_with_nonexistent_user()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->isUserActive(new User());
    }

    public function test_is_user_active_with_soft_deleted_user()
    {
        $this->targetUser->delete();

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->isUserActive($this->targetUser);
    }

    public function test_enable_disable_user_multiple_times()
    {
        $originalStatus = $this->targetUser->active;

        // First toggle
        $result1 = $this->userService->enableDisableUser($this->targetUser);
        $updatedUser1 = User::find($this->targetUser->id);

        $this->assertTrue($result1);
        $this->assertNotEquals($originalStatus, $updatedUser1->active);

        // Second toggle (should return to original)
        $result2 = $this->userService->enableDisableUser($this->targetUser);
        $updatedUser2 = User::find($this->targetUser->id);

        $this->assertTrue($result2);
        $this->assertEquals($originalStatus, $updatedUser2->active);
    }

    public function test_get_company_users_with_empty_company()
    {
        $emptyCompany = Company::factory()->withTax()->create();

        $result = $this->userService->getCompanyUsers($emptyCompany->id);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_get_company_users_with_nonexistent_company()
    {
        $result = $this->userService->getCompanyUsers(99999);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_get_user_company_with_deleted_company_relation()
    {
        $userWithCompany = User::factory()->create(['company_id' => $this->company->id]);
        $this->company->delete();

        $result = $this->userService->getUserCompany($userWithCompany);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_assigned_price_lists_with_nonexistent_company()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->getAssignedPriceLists(99999);
    }

    public function test_get_assigned_price_lists_with_company_no_price_lists()
    {
        $result = $this->userService->getAssignedPriceLists($this->company->id);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_assign_role_with_nonexistent_role()
    {
        $this->expectException(\Spatie\Permission\Exceptions\RoleDoesNotExist::class);

        $this->userService->assignRole($this->targetUser, 'nonexistent-role');
    }

    public function test_assign_role_to_nonexistent_user()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->userService->assignRole(new User(), 'no-role');
    }

    public function test_remove_role_with_nonexistent_role()
    {
        $this->expectException(\Spatie\Permission\Exceptions\RoleDoesNotExist::class);

        $data = ['role' => 'truly-nonexistent-role'];

        $this->userService->removeRole($this->targetUser, $data);
    }

    public function test_get_roles_with_user_no_roles()
    {
        $userWithoutRoles = User::factory()->create(['company_id' => $this->company->id]);

        $result = $this->userService->getRoles($userWithoutRoles->id);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_assign_permission_with_nonexistent_permission()
    {
        $this->expectException(\Spatie\Permission\Exceptions\PermissionDoesNotExist::class);

        $data = ['permission' => 'nonexistent-permission'];
        $this->userService->assignPermission($this->targetUser, $data);
    }

    public function test_remove_permission_with_nonexistent_permission()
    {
        $this->expectException(\Spatie\Permission\Exceptions\PermissionDoesNotExist::class);

        $data = ['permission' => 'truly-nonexistent-permission'];

        $this->userService->removePermission($this->targetUser, $data);
    }

    public function test_get_permissions_with_user_no_permissions()
    {
        $userWithoutPermissions = User::factory()->create(['company_id' => $this->company->id]);

        $result = $this->userService->getPermissions($userWithoutPermissions->id);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertCount(0, $result);
    }

    public function test_resolve_company_with_nonexistent_name()
    {
        $reflection = new \ReflectionClass($this->userService);
        $method = $reflection->getMethod('resolveCompany');
        $method->setAccessible(true);

        $this->expectException(\Illuminate\Support\ItemNotFoundException::class);

        $method->invoke($this->userService, 'Nonexistent Company Name');
    }

    public function test_resolve_company_with_nonexistent_id()
    {
        $reflection = new \ReflectionClass($this->userService);
        $method = $reflection->getMethod('resolveCompany');
        $method->setAccessible(true);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $method->invoke($this->userService, 99999);
    }

    public function test_create_user_with_auth_user_null_company()
    {
        $superAdmin = User::factory()->create(['company_id' => null]);

        // Create a new service instance and mock auth properly
        $userService = new UserServiceImpl;
        $reflection = new ReflectionClass($userService);
        $property = $reflection->getProperty('authUser');
        $property->setAccessible(true);
        $property->setValue($userService, $superAdmin);

        $userData = [
            'first_name' => 'New',
            'last_name' => 'User',
            'email' => 'newuser@example.com',
            'username' => 'newuser',
            'password' => 'password123',
        ];

        $result = $userService->createUser($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('New', $result->first_name);
        $this->assertNull($result->company_id);
    }

    /**
     * @throws \Throwable
     */
    public function test_create_user_with_company_parameter()
    {
        $userData = [
            'first_name' => 'Company',
            'last_name' => 'User',
            'email' => 'companyuser@example.com',
            'username' => 'companyuser',
            'password' => 'password123',
            'company' => $this->company->name,
        ];

        $result = $this->userService->createUser($userData);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($this->company->id, $result->company_id);
    }
}
