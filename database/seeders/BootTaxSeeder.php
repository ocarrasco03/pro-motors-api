<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Seeder;

class BootTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            ['name' => 'IVA 8%', 'type' => 'IVA', 'description' => 'IVA 8%', 'rate' => 0.08],
            ['name' => 'IVA 16%', 'type' => 'IVA', 'description' => 'IVA 16%', 'rate' => 0.16]
        ];

        foreach ($taxes as $tax) {
            Tax::firstOrCreate($tax);
        }
    }
}
