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

        $user = User::create([
            'name' => 'Sudo Chris',
            'email' => 'sudochris',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'office_admin' => true,
            'password' => Hash::make('foolhardy'),
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

        $user5 = User::create([
            'name' => 'VICTORIA AYAUNOR',
            'email' => 'victoria',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('love'),
            'sales_executive' => true
        ]);

        $user6 = User::create([
            'name' => 'MARCELLINUS UTU',
            'email' => 'MARCELO',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('Marcel2020'),
            'sales_executive' => true
        ]);
        
        $user7 = User::create([
            'name' => 'WILSON GUANAH',
            'email' => 'wilson',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('doncent1987'),
            'sales_executive' => true
        ]);

        $user8 = User::create([
            'name' => 'JONATHAN UGBOKO            ',
            'email' => 'JONATHAN',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('doublej01'),
            'sales_executive' => true
        ]);

        $user9 = User::create([
            'name' => 'AMALUWE AZUKA',
            'email' => 'azuka',
            'phone' => '0707220224',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('AMALUWE12'),
            'sales_executive' => true
        ]);

        $user2 = User::create([
            'name' => 'ONOTU LUCKY EFE',
            'email' => 'desmond',
            'phone' => '08162669942',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('123@Team'),
            'sales_executive' => true
        ]);

        $user18 = User::create([
            'name' => 'PRECIOUS ONOYIMA',
            'email' => 'PRECIOUS2',
            'phone' => '08159943932',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('preci4xrist'),
            'sales_executive' => true
        ]);

        $user19 = User::create([
            'name' => 'MICHAEL IHEAKAMADU',
            'email' => 'miheakamadu',
            'phone' => '08159943932',
            'email_verified_at' => now(),
            'branch' => 'Asaba',
            'password' => Hash::make('onyedikachi1'),
            'sales_executive' => true
        ]);

        
        $user->assignRole('Sales Executive');
        $user1->assignRole('Office Administrator');
    }
}
