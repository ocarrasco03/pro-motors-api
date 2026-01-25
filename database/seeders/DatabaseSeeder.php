<?php

namespace Database\Seeders;

use App\Core\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BootTaxSeeder::class,
            BootRolesPermissionsSeeder::class,
        ]);

        $user = User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'username' => 'test',
        ]);

        $user->assignRole(RolesEnum::SUPER_ADMIN);
    }
}
