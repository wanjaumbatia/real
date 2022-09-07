<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Sales Executive']);
        Role::create(['name' => 'Office Administrator']);
        Role::create(['name' => 'Branch Manager']);
        Role::create(['name' => 'Assistant Branch Manager']);
        Role::create(['name' => 'Managing Director']);
    }
}
