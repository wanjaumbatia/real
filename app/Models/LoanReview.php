<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanReview extends Model
{
    use HasFactory;

    protected $fillable = ['commulative_remarks', 'action_plan', 'loan_id'];
}
