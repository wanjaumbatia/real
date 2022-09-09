<?php

namespace Database\Seeders;

use App\Models\BankAccounts;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer1 = Customer ::create([
            'name'=>'Francis Mbatia',
            'address'=>'Asaba',
            'gender'=>'Male',
            'town'=>'Asaba',
            'phone'=>'0707220224',
            'posted'=>false,
            'no'=>'000001',
            'dob'=>now(), 
            'handler'=>'VICTORIA AYAUNOR',
            'address'=>'Behind the market',
            'branch'=>'Asaba',
            'business'=>'',
            'created_by'=>'mbatia'
        ]);

        $bank_account1 = BankAccounts::create([
            'bank_name'=>'Wema Bank Asaba',
            'bank_account'=>'1234567',
            'bank_branch'=>'Asabe',
            'created_by'=>now(),
            'customer_id'=>'1'
        ]);

        $bank_account = BankAccounts::create([
            'bank_name'=>'GTB Bank Asaba',
            'bank_account'=>'876543',
            'bank_branch'=>'Asabe',
            'created_by'=>now(),
            'customer_id'=>'1'
        ]);

       
    }
}
