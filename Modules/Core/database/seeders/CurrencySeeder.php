<?php
namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Currency;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::updateOrCreate(
            ['name' => 'Iranian Rial'],
            [
                'symbol' => 'IRR',
                'display_name' => 'ریال',
                'divisor' => 1,
                'is_active' => true, // ریال به صورت پیش‌فرض فعال است
            ]
        );

        Currency::updateOrCreate(
            ['name' => 'Iranian Toman'],
            [
                'symbol' => 'IRT',
                'display_name' => 'تومان',
                'divisor' => 10,
                'is_active' => false,
            ]
        );
    }
}
