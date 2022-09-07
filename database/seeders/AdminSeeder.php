<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Francis Mbatia',
            'email' => 'mbatia',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'admin' => true,
            'password' => Hash::make('123@Team'),
        ]);

        $user = User::create([
            'name' => 'David Owuor',
            'email' => 'david',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'office_admin' => true,
            'password' => Hash::make('123@Team'),
        ]);

        $user1 = User::create([
            'name' => 'KINGSLEY UMO ESIN',
            'email' => 'kingsley',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('123@Team'),
            'sales_executive' => true
        ]);

        $user1 = User::create([
            'name' => 'ONOTU LUCKY EFE',
            'email' => 'desmond',
            'phone' => '08162669942',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('123@Team'),
            'sales_executive' => true
        ]);

        
        $user->assignRole('Sales Executive');
        $user1->assignRole('Office Administrator');
    }
}
