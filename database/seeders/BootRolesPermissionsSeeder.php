<?php

namespace Database\Seeders;

use App\Domain\ValueObjects\Enums\PermissionsEnum;
use App\Domain\ValueObjects\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BootRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (RolesEnum::cases() as $role) {
            Role::findOrCreate($role->value);
        }

        $permissions = collect(PermissionsEnum::cases())
            ->map(fn($permission) => Permission::findOrCreate($permission->value))
            ->filter();

        $superAdminRole = Role::findByName(RolesEnum::SUPER_ADMIN->value);
        $adminRole = Role::findByName(RolesEnum::ADMIN->value);
        $managerRole = Role::findByName(RolesEnum::MANAGER->value);
        $userRole = Role::findByName(RolesEnum::USER->value);

        $superAdminRole?->syncPermissions($permissions);

        $adminPermissions = $permissions
            ->filter(function ($permission) {
                if(!str_contains($permission->name, 'delete')) {
                    return true;
                }

                return in_array($permission->name, [
                    PermissionsEnum::DELETE_USER->value
                ], true);
            });
        $adminRole?->syncPermissions($adminPermissions);

        $managerPermissions = $permissions
            ->filter(fn ($permission) => str_contains($permission->name, 'view') || str_contains($permission->name, 'edit'));
        $managerRole?->syncPermissions($managerPermissions);

        $userPermissions = $permissions
            ->filter(fn ($permission) => str_contains($permission->name, 'view'));
        $userRole?->syncPermissions($userPermissions);
    }
}
