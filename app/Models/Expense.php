<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch', 'description', 'status', 'approved', 'amount', 'remarks', 'created_by', 'type'
    ];
}
