<?php

namespace Modules\Treasury\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Treasury\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'هزینه‌های عمومی و اداری'],
            ['name' => 'هزینه حمل و نقل'],
            ['name' => 'حقوق و دستمزد'],
            ['name' => 'پذیرایی و آبدارخانه'],
            ['name' => 'اجاره و شارژ ساختمان'],
            ['name' => 'بازاریابی و تبلیغات'],
            ['name' => 'پیک'],
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate($category);
        }
    }
}
