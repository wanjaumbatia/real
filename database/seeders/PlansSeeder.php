<?php

namespace Database\Seeders;

use App\Models\Plans;
use Illuminate\Database\Seeder;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $plan_regular = Plans::Create([
            'name'=>'Regular',
            'code'=>'REGULAR',
            'default'=>true,
            'charge'=>0.03,
            'reimbursement'=>0,
            'allow_multiple'=>true,
            'sep_commission'=>0.18,
            'penalty'=>0,
            'duration'=>0,
            'create_by'=>'Admin',
            'outward'=>false
        ]);

        $plan_gold = Plans::Create([
            'name'=>'Real Savings Gold',
            'code'=>'GOLD',
            'default'=>false,
            'duration'=>3,
            'penalty'=>4.5,
            'charge'=>0.03,            
            'sep_commission'=>0.18,
            'reimbursement'=>0,
            'allow_multiple'=>true,
            'create_by'=>'Admin',
            'outward'=>false
        ]);

        $plan_diamond = Plans::Create([
            'name'=>'Real Savings Diamond',
            'code'=>'DIAMOND',
            'default'=>false,
            'duration'=>6,
            'penalty'=>4.5,
            'charge'=>0.03,
            'sep_commission'=>0.18,
            'reimbursement'=>50,
            'allow_multiple'=>true,
            'create_by'=>'Admin',
            'outward'=>true
        ]);

        $plan_platinum = Plans::Create([
            'name'=>'Real Savings Platinum',
            'code'=>'PLATINUM',
            'default'=>false, 
            'duration'=>12,
            'penalty'=>4.5,
            'sep_commission'=>0.18,
            'charge'=>0.03,
            'reimbursement'=>100,
            'allow_multiple'=>true,
            'create_by'=>'Admin',
            'outward'=>true
        ]);
    }
}
