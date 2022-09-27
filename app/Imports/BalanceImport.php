<?php

namespace App\Imports;

use App\Models\CommissionLines;
use App\Models\Customer;
use App\Models\Payments;
use App\Models\SavingsAccount;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class BalanceImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        //get account details
        $customer = Customer::where('phone', "0" . $row['phone'])->first();
        $acc = SavingsAccount::where('customer_id', $customer->id)->first();
        $batch_number = rand(100000000, 999999999);
        $reference = rand(100000000, 999999999);
        $user = User::where('name', $customer->handler)->first();

        Log::info($acc);
        // $payment = Payments::create([
        //     'savings_account_id' => $acc->id,
        //     'plan' => $acc->plan,
        //     'customer_id' => $acc->customer_id,
        //     'customer_name' => $acc->customer,
        //     'transaction_type' => 'savings',
        //     'status' => 'pending',
        //     'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($row['amount'], 2),
        //     'debit' => $row['amount'],
        //     'credit' => 0,
        //     'amount' => $row['amount'],
        //     'requires_approval' => false,
        //     'approved' => false,
        //     'posted' => false,
        //     'created_by' => $customer->handler,
        //     'branch' => $user->branch,
        //     'batch_number' => $batch_number,
        //     'reference' => $reference,
        //     'created_at' => $row['date']
        // ]);

        // $sep_commision = 0.0025 * $row['amount'];
        // $comm_line = CommissionLines::create([
        //     'handler' => $customer->handler,
        //     'amount' => $sep_commision,
        //     'description' => '3Commission for sales of ₦' . number_format($row['amount'], 2) . ' for ' . $acc->customer,
        //     'batch_number' => $batch_number,
        //     'payment_id' => $payment->id,
        //     'disbursed' => false,
        //     'branch' => $user->branch,
        //     'transaction_type' => 'savings',
        //     'approved' => false,
        //     'created_at' => $row['date']
        //     // 'transaction_type'=>'commission'
        // ]);

        return [];
    }

    public function batchSize(): int
    {
        return 1;
    }

    public function chunkSize(): int
    {
        return 1;
    }
}
