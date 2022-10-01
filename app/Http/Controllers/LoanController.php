<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if($user->loan_officer!=true){
            return abort(401);
        }else{
            $loans = Loan::all();
            return view('loans.index')->with(['loans'=>$loans]);
        }       
    }

    public function loan_recon_list($id){
        return view('loans.loan_recon_list');
    }

}
