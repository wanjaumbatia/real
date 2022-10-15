<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'branch',
        'remarks',
        'category',
        'amount',
        'outward',
        'inward',
        'to',
        'from'
    ];
    
}
