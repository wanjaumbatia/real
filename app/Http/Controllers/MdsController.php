<?php

namespace App\Http\Controllers;

use App\Imports\ExpenseCode;
use App\Imports\ExpenseCodeImport;
use App\Models\Branch;
use App\Models\CashSummary;
use App\Models\ExpenseType;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MdsController extends Controller
{
    public function index()
    {
        return view('md.index');
    }

    public function import_expense_codes()
    {
        $codes = ExpenseType::all();
        return view('md.import_expenses_codes')->with(['codes' => $codes]);
    }

    public function post_expense_code_excel(Request $request)
    {
        Excel::import(new ExpenseCodeImport, $request->file);
        return redirect()->to('/import_expense_codes');
    }


    public function generate_prev_summary(Request $request)
    {

        $branches = Branch::all();
        $dates = ['2022-10-01', '2022-10-15'];

        for ($i = 0; $i < count($branches); $i++) {
            $bran = $branches[$i];
            $dt = DB::select("select 
               (select sum(amount) from payments where remarks != 'Opening Balance' and transaction_type = 'savings' and status = 'confirmed' and created_at < '2022-10-01' and created_at > '2022-09-26' and branch = '" . $branches[$i]->name . "') as remitance,
               (select sum(amount) from payments where remarks != 'Opening Balance' and transaction_type = 'withdrawal' and status = 'confirmed' and created_at < '2022-10-01' and created_at > '2022-09-26' and branch = '" . $branches[$i]->name . "') as withdrawal,
               (select sum(loan_amount) from loans_models where start_date > '2022-09-25' and start_date < '2022-10-01' and loan_status = 'ACTIVE' and branch='" . $branches[$i]->name . "') as loan
                from branches LIMIT 1");

            $remitance = $dt[0]->remitance;
            $withdrawals = $dt[0]->withdrawal;
            $loan = 0;
            if ($dt[0]->loan != null) {
                $loan = $dt[0]->loan;
            }

            $cs = CashSummary::create([
                'report_date' => '2022-09-30',
                'Remmitance' => $remitance,
                'Withdrawals' => $withdrawals * -1,
                'LoanIssued' => $loan,
                'branch' => $branches[$i]->name
            ]);
        }

        return response('Done');
    }
}
