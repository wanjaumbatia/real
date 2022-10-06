<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\LoansModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LoansModelImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $customer = Customer::where('username', $row['username'])->first();
        $name = $customer->name;
        $customer_id = $customer->id;
        $branch = $customer->branch;
        $handler = $customer->handler;

        return new LoansModel([
            'application_date' => $row['start_date'],
            'start_date' => $row['start_date'],
            'exit_date' => $row['exit_date'],
            'disbursed' => true,
            'customer' => $name,
            'customer_id' => $customer_id,
            'username' => $row['username'],
            'handler' => $handler,
            'branch' => $branch,
            'loan_amount' => $row['loan_amount'],
            'percentage' => $row['percentage'],
            'remarks' => 'Imported from old system',
            'duration' => $row['duration'],
            'disbursement_mode' => 'imported',

            'total_interest' => $row['total_interest'],
            'total_interest_paid' => $row['total_interest_paid'],
            'monthly_interest' => $row['monthly_interest'],
            'monthly_interest_paid' => 0,
            'monthly_principle' => $row['monthly_principle'],
            'monthly_principle_paid' => 0,
            'total_monthly_payment' => $row['monthly_principle']+$row['monthly_interest'],
            'total_monthly_paid' => 0,
            'total_monthly_balance' => $row['monthly_principle']+$row['monthly_interest'],
            'total_amount_paid' => $row['total_amount_paid'],
            'capital_balance' => 0,
            'total_balance' => $row['total_balance'],
            
            'loan_status' => $row['loan_status'],
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
