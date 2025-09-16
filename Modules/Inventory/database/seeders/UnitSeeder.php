<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'عدد', 'abbreviation' => 'عدد'],
            ['name' => 'کیلوگرم', 'abbreviation' => 'ک.گ'],
            ['name' => 'متر', 'abbreviation' => 'متر'],
            ['name' => 'بسته', 'abbreviation' => 'بسته'],
            ['name' => 'کارتن', 'abbreviation' => 'کارتن'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
