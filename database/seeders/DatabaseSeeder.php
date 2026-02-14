<?php

namespace Database\Seeders;

use App\Domain\ValueObjects\Enums\RolesEnum;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\BootTaxSeeder;
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

        if (config('app.env') !== 'production') {

            $company = Company::factory()
                ->withTax()
                ->active()
                ->create();

            $user = User::factory()->create([
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'email' => 'test@example.com',
                'username' => 'testAdmin',
                'company_id' => $company->id,
            ]);

            $user->assignRole(RolesEnum::ADMIN);

            $user = User::factory()->create([
                'first_name' => 'Test',
                'last_name' => 'Normal User',
                'email' => 'test@example.com',
                'username' => 'testUser',
                'company_id' => $company->id,
            ]);

            $user->assignRole(RolesEnum::USER);

            User::factory(25)->withCompany($company->id)->create();
            Company::factory(10)->withTax()->create();
        }
    }
}
