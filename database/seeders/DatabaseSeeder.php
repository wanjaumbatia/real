<?php

namespace Database\Seeders;

use App\Models\LoanDeduction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // $this->call(RoleSeeder::class);
        // $this->call(AdminSeeder::class);
        // $this->call(BranchSeeder::class);
        // // $this->call(CustomerSeeder::class);
        // $this->call(PlansSeeder::class);\
        $this->call(AdminSeeder::class);
    }
}
