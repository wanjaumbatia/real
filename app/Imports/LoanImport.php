<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Loan;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LoanImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        Log::warning($row['username']);
        $customer = Customer::where('username', $row['username'])->first();
        Log::warning($customer);
        return new Loan([
            'no' => $customer->no,
            'name' => $customer->name,
            'customer_id' => $customer->id,
            "application_date" => $row['application_date'],
            "amount" => $row['amount'],
            "interest_percentage" => $row['percent'],
            "duration" => $row['duration'],
            "current_savings" => 0,
            "handler" => $row['handler'],
            "purpose" => "",
            "status" => "running",
            "remarks" => "Imported From Old System",
            "posted" => true,
            "date_posted" => $row['disbursement_date'],
            'paid' => $row['paid']
        ]);
    }

    public function batchSize(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 2;
    }
}
