<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date', 'opening_balance', 'Remmitance', 'CashInflow', 'CashOutflow',
        'Expense', 'Withdrawals', 'LoanIssued', 'TotalCashIn', 'TotalCashOut',
        'CashbookBalance', 'CashAtHand', 'Shortage', 'branch', 'admin_remarks',
        'operations_head_remarks'
    ];
        
}
