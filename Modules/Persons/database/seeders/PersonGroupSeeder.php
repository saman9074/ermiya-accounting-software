<?php

namespace Modules\Persons\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\PriceList;
use Modules\Persons\Models\PersonGroup;

class PersonGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collaboratorPriceList = PriceList::where('name', 'قیمت همکار')->first();

        $groups = [
            ['name' => 'عادی', 'description' => 'مشتریان عمومی'],
            // اگر لیست قیمت همکار وجود داشت، آن را به گروه همکار متصل می‌کنیم
            ['name' => 'همکار', 'description' => 'همکاران و فروشندگان', 'price_list_id' => $collaboratorPriceList?->id],
            ['name' => 'ویژه', 'description' => 'مشتریان خاص'],
        ];

        foreach ($groups as $group) {
            PersonGroup::firstOrCreate(['name' => $group['name']], $group);
        }
    }
}
