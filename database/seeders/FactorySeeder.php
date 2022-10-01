<?php

namespace Database\Seeders;

use App\Models\LoanDeduction;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = LoanDeduction::create([
            ''
        ]);
    }
}
