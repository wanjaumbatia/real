<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
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
            'sales_executive' => true
        ]);
        
    }

    
    public function batchSize(): int
    {
        return 3;
    }

    public function chunkSize(): int
    {
        return 3;
    }
}
