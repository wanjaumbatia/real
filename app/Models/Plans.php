<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','code','default','charge','allow_multiple','create_by','outward','duration','penalty','reimbursement','active'
    ];
}
