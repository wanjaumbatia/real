<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CustomerImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Customer([
            'no' => get_customer_number(),
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'handler' => $row['handler'],
            'username' => $row['username'],
            'created_by' => auth()->user()->name
        ]);
    }

    public function batchSize(): int
    {
        return 200;
    }

    function get_customer_number()
    {
        $customers = Customer::all();
        $no = count($customers) + 1;
        return str_pad($no, 6, '0', STR_PAD_LEFT);
    }

    public function chunkSize(): int
    {
        return 200;
    }
}
