<?php

namespace Database\Seeders;

use App\Models\LoanDeduction;
use Illuminate\Database\Seeder;

class DeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        LoanDeduction::create([
            'name' => 'Management Fees',
            'percentange' => true,
            'amount'=>0,
            'percentange_amount' => 3.225,
            'active' => true
        ]);

        LoanDeduction::create([
            'name' => 'Insurance ',
            'percentange' => true,
            'amount'=>0,
            'percentange_amount' => 1.075,
            'active' => true
        ]);

        LoanDeduction::create([
            'name' => 'Recovery',
            'percentange' => false,
            'percentange_amount'=>0,
            'amount'=>0,
            'amount' => 1075    ,
            'active' => true
        ]);

        LoanDeduction::create([
            'name' => 'Form fees',
            'percentange' => false,
            'percentange_amount'=>0,
            'amount'=>0,
            'amount' => 1075    ,
            'active' => true
        ]);
    }
}
