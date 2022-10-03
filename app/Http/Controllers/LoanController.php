<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDeduction;
use App\Models\LoanForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
}
