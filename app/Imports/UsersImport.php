<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        
        return new User([
            'name' => $row['name'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($row['password']),
            'branch' => $row['branch'],
            'office_admin' => true
        ]);
        
    }
}
