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
            'name' => 'BEST UFUOMA ERHABOR',
            'email' => 'best',
            'phone' => '',
            'email_verified_at' => now(),
            'branch' => 'Delta',
            'loan_officer' => true,
            'password' => Hash::make('NENENE3247'),
        ]);

        $user = User::create([
            'name' => 'MICHAEL ABIA',
            'email' => 'abia4real',
            'phone' => '',
            'email_verified_at' => now(),
            'branch' => 'Delta',
            'loan_officer' => true,
            'password' => Hash::make('abia1986'),
        ]);
    }
}
