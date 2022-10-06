<?php

namespace App\Http\Controllers;

use App\Imports\LoansModelImport;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDeduction;
use App\Models\LoanForm;
use App\Models\LoanLedgerEntries;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentModel;
use App\Models\LoansModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->status == null || $request->status == 'all') {
            $data = DB::select("
            select 
                    loans.id, 
                    loans.name, 
                    loans.application_date,
                    loans.customer_id,
                    loans.amount, 
                    loans.paid,
                    loans.interest_percentage,
                    loans.duration,
                    loans.handler,
                    loans.status,
                    loans.remarks,
                    users.branch,
                    loans.current_savings
                from loans inner join users on  loans.handler = users.name
            ");
        } else {
            $data = DB::select("
            select 
                    loans.id, 
                    loans.name, 
                    loans.application_date,
                    loans.customer_id,
                    loans.amount, 
                    loans.paid,
                    loans.interest_percentage,
                    loans.duration,
                    loans.handler,
                    loans.status,
                    loans.remarks,
                    users.branch ,
                    loans.current_savings
                from loans inner join users on  loans.handler = users.name and status = '" . $request->status . "';
            ");
        }


        return view('loans.index')->with(['loans' => $data, 'status' => $request->status]);
    }

    public function charge_interest(Request $request)
    {
        //get loan
        $loans = LoansModel::where('loan_status', 'active')->get();
        foreach ($loans as $loan) {
            $customer = Customer::where('id', $loan->customer_id)->first();
            //check if its charge date
            if (Carbon::now()->startOfDay()->gte($loan->next_charge_date) || $loan->next_charge_date == null) {
                $interest = $loan->loan_amount * $loan->percentage / 100;
                LoanLedgerEntries::create([
                    'loan_model_id' => $loan->id,
                    'customer_id' => $customer->id,
                    'customer' => $customer->name,
                    'handler' => $customer->handler,
                    'branch' => $customer->branch,
                    'remarks' => 'Interest Charge',
                    'debit' => $interest,
                    'credit' => 0,
                    'amount' => $interest
                ]);

                $dt = LoansModel::where('id', $loan->id)->update([
                    'total_interest' => $loan->total_interest + $interest,
                    'total_balance' => $loan->total_balance + $interest,
                    'next_charge_date' => Carbon::now()->addMonth()
                ]);
            }
        }

        $loan = LoansModel::where('id', '858')->first();
        $statement = LoanLedgerEntries::where('loan_model_id', $loan->id)->get();
        return view('sales.loan_card')->with([
            'customer' => $customer, 'loan' => $loan, 'statement' => $statement
        ]);
    }

    public function loan_repayment(Request $request){
        //go to loan repayment table and pick confirmed but not posted loans
        $repayment = LoanRepaymentModel::where('status', 'confirmed')->where('posted', false)->get();
        dd($repayment);
    }


    public function request(Request $request)
    {
        $loans = Loan::where('status', 'processing')->get();
        return view('loans.requests')->with(['loans' => $loans]);
    }

    public function processing_loan_card(Request $request, $id)
    {
        //get loan details
        $loan = Loan::where('id', $id)->first();
        //get customer details
        $customer = Customer::where('id', $loan->customer_id)->first();
        $identity = false;
        $photo = false;
        $form = false;
        $guarantor = false;
        $agreement = false;
        $loan_forms = LoanForm::where('loan_id', $id)->get();
        foreach ($loan_forms as $item) {
            if ($item->title == 'ID Card') {
                $identity = true;
            }

            if ($item->title == 'Passport Photo') {
                $photo = true;
            }

            if ($item->title == 'Loan') {
                $form = true;
            }

            if ($item->title == 'Guarantor') {
                $guarantor = true;
            }

            if ($item->title == 'Agreement') {
                $agreement = true;
            }
        }
        //create charges
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();
        foreach ($deduction as $item) {
            $rec = array();
            $rec['name'] = $item->name;
            if ($item->percentange == true) {
                $rec['amount'] = $loan->amount * ($item->percentange_amount / 100);
            } else {
                $rec['amount'] = $item->amount;
            }
            $deductions[] = $rec;
        }

        //calculate payments

        return view('loans.processing_loan')
            ->with([
                'loan' => $loan,
                'customer' => $customer,
                'loan_forms' => $loan_forms,
                'identity' => $identity,
                'photo' => $photo,
                'guarantor' => $guarantor,
                'agreement' => $agreement,
                'form' => $form,
                'deductions' => $deductions,
                'loan_forms' => $loan_forms
            ]);
    }

    public function download_file(Request $request)
    {
        if (Storage::disk('loan_docs')->exists(""));
    }

    public function loan_officer_approval(Request $request, $id)
    {
        $loan = Loan::where('id', $id)->first();
        $ln = Loan::where('id', $id)->update([
            'status' => 'processing',
            'loan_officer_approval' => true,
            'loan_officer_remarks' => $request->comment
        ]);

        return redirect()->to('/home');
    }

    public function ImportLoans(Request $request)
    {
        Excel::import(new LoansModelImport, $request->file);
        return "done";
    }
}
