<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id', 'url', 'title'
    ];
}
