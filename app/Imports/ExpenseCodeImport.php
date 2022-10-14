<?php

namespace App\Imports;

use App\Models\ExpenseType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExpenseCodeImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        return new ExpenseType([
            'expense_type' => $row['code'],
            'category' => $row['category'],
            'credit' => true
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
