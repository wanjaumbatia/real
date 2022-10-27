<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $branch1 = Branch::create([
            'name' => 'Asaba'
        ]);
        $branch100 = Branch::create([
            'name' => 'Abraka'
        ]);
        $branch2 = Branch::create([
            'name' => 'Agbarho'
        ]);
        $branch3 = Branch::create([
            'name' => 'Agbor'
        ]);

        $branch5 = Branch::create([
            'name' => 'Issele-Uku'
        ]);

        $branch6 = Branch::create([
            'name' => 'Koko'
        ]);

        $branch7 = Branch::create([
            'name' => 'Kwale'
        ]);

        $branch8 = Branch::create([
            'name' => 'Obiaruku'
        ]);

        $branch9 = Branch::create([
            'name' => 'Oghara'
        ]);

        $branch10 = Branch::create([
            'name' => 'Ogwashi-Uku'
        ]);

        $branch11 = Branch::create([
            'name' => 'Oleh'
        ]);

        $branch12 = Branch::create([
            'name' => 'Ozoro'
        ]);

        $branch13 = Branch::create([
            'name' => 'Sapele'
        ]);

        $branch14 = Branch::create([
            'name' => 'Ughelli'
        ]);

        $branch15 = Branch::create([
            'name' => 'Warri'
        ]);
    }
}
