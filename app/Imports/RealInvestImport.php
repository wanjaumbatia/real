<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\RealInvest;
use App\Models\SavingsAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RealInvestImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        $customer = Customer::where('username', $row['username'])->first();
        $percentage = 0;
        if ($row['tenure'] == 6) {
            $percentage = 10;
        } else if ($row['tenure'] == 12) {
            $percentage = 22;
        } else if ($row['tenure'] == 18) {
            $percentage = 35;
        } else if ($row['tenure'] == 24) {
            $percentage = 50;
        } else {
        }
        $plan = Plans::where('name', 'Real Invest')->first();

        $batch_number = rand(100000000, 999999999);
        $reference = rand(100000000, 999999999);

        $invest = RealInvest::create([
            'plan_name' => 'Real Invest',
            'is_customer' => true,
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'handler' => $row['handler'],
            'amount' => $row['amount'],
            'duration' => $row['tenure'],
            'percentage' => $percentage,
            'status' => 'ACTIVE',
            'branch' => $customer->branch,
            'start_date' => $row['start_date'],
            'exit_date' => $row['exit_date']
        ]);

        $acc = SavingsAccount::create([
            'customer_id' => $customer->id,
            'customer_number' => $customer->no,
            'plans_id' => $plan->id,
            'name' => $plan->name,
            'pledge' => 0,
            'created_by' => $customer->handler,
            'active' => true,
            'branch' => $customer->branch,
            'handler' => $customer->handler,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);

        $payment = Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'confirmed',
            'remarks' => 'Real Invest payment for ' . $acc->customer . ' of ₦' . number_format($invest->amount, 2),
            'debit' => $invest->amount,
            'credit' => 0,
            'amount' => $invest->amount,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $customer->handler,
            'branch' => $customer->branch,
            'batch_number' => $batch_number,
            'reference' => $reference,
            'created_at' => $row['start_date'],
            'reconciled'=>true
        ]);
        return [];
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
