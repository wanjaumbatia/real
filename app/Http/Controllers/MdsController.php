<?php

namespace App\Http\Controllers;

use App\Imports\ExpenseCode;
use App\Imports\ExpenseCodeImport;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MdsController extends Controller
{
    public function index(){
        return view('md.index');
    }
    
    public function import_expense_codes(){
        $codes = ExpenseType::all();
        return view('md.import_expenses_codes')->with(['codes'=>$codes]);
    }

    public function post_expense_code_excel(Request $request){        
        Excel::import(new ExpenseCodeImport, $request->file);
        return redirect()->to('/import_expense_codes');
    }
}
