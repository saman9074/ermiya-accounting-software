<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\PriceList;

class PriceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priceLists = [
            ['name' => 'قیمت فروش'],
            ['name' => 'قیمت همکار'],
            ['name' => 'قیمت عمده'],
        ];

        foreach ($priceLists as $priceList) {
            PriceList::firstOrCreate($priceList);
        }
    }
}
