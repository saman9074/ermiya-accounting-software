<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SeedDefaultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ermiya:seed-defaults';

    /**
     * The console command description.
     */
    protected $description = 'Seed the database with default application values.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding default application values...');

        // فراخوانی Seederهای هر ماژول
        Artisan::call('module:seed', ['module' => 'Inventory', '--class' => 'UnitSeeder']);
        $this->info('Default units seeded.');

        Artisan::call('module:seed', ['module' => 'Inventory', '--class' => 'PriceListSeeder']);
        $this->info('Default price lists seeded.');

        // Seeder گروه اشخاص بعد از سطوح قیمت اجرا شود
        Artisan::call('module:seed', ['module' => 'Persons', '--class' => 'PersonGroupSeeder']);
        $this->info('Default person groups seeded.');

        Artisan::call('module:seed', ['module' => 'Treasury', '--class' => 'ExpenseCategorySeeder']);
        $this->info('Default expense categories seeded.');

        $this->info('All default values have been seeded successfully!');

        return self::SUCCESS;
    }
}
