<?php

namespace App\Imports;

use App\Models\Payments;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AccImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        $x = DB::table('payments')->insert([
            'id' => $row['id'],
            'savings_account_id' => $row['savings_account_id'],
            'plan' => $row['plan'],
            'customer_id' => $row['customer_id'],
            'customer_name' => $row['customer_name'],
            'transaction_type' => $row['transaction_type'],
            'status' => $row['status'],
            'remarks' => $row['remarks'],
            'debit' => $row['debit'],
            'credit' => 0,
            'amount' => $row['amount'],
            'requires_approval' => $row['requires_approval'],
            'approved' => $row['approved'],
            'posted' => $row['posted'],
            'created_by' => $row['created_by'],
            'cps' => $row['cps'],
            'first_approver' => $row['first_approver'],
            'second_approver' => $row['second_approver'],
            'sent_cps' => $row['sent_cps'],
            'batch_number' => $row['batch_number'],
            'branch' => $row['branch'],
            'reference' => $row['reference'],
            'reconciled' => $row['reconciled'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'reconciliation_reference' => $row['reconciliation_reference'],
            'reconciled_by' => $row['reconciled_by'],
            'admin_reconciled' => $row['admin_reconciled'],
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
