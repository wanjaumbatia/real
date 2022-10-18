<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RealSavingImport implements ToModel,  WithHeadingRow, WithChunkReading
{
    public function model(array $row)
    {
        dd($row);
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
