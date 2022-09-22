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
    }
}
