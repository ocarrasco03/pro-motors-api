<?php

namespace Database\Seeders;

use App\Core\Enums\PermissionsEnum;
use App\Core\Enums\RolesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            ->filter(fn ($permission) => !str_contains($permission->name, 'delete') || str_contains($permission->name, 'delete.user'));
        $adminRole?->syncPermissions($adminPermissions);

        $managerPermissions = $permissions
            ->filter(fn ($permission) => str_contains($permission->name, 'view') || str_contains($permission->name, 'edit'));
        $managerRole?->syncPermissions($managerPermissions);

        $userPermissions = $permissions
            ->filter(fn ($permission) => str_contains($permission->name, 'view'));
        $userRole?->syncPermissions($userPermissions);
    }
}
