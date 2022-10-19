<?php

namespace App\Http\Controllers;

use App\Imports\AccImport;
use App\Imports\LoansModelImport;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDeduction;
use App\Models\LoanForm;
use App\Models\LoanLedgerEntries;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentModel;
use App\Models\LoanReview;
use App\Models\LoanSecurityType;
use App\Models\LoansModel;
use App\Models\Payments;
use App\Models\SavingsAccount;
use App\Models\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LoanController extends Controller
{


    public function __construct()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '12000');
        ini_set('request_terminate_time', '12000');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->status == null || $request->status == 'all') {
            $data = LoansModel::all();
        } else {
            $data = LoansModel::where('loan_status', $request->status)->get();
        }

        foreach ($data as $ln) {
            $now = Carbon::now();
            $diff =  Carbon::parse($now)->diffInDays($ln->exit_date);

            $ln->savings = Payments::where('customer_name', $ln->customer)->sum('amount');

            if ($ln->exit_date < $now) {
                $ln->countdown =  $diff * -1;
            } else {
                $ln->countdown = $diff;
            }
        }

        return view('loans.index')->with(['loans' => $data, 'status' => $request->status]);
    }

    public function charge_interest(Request $request)
    {
        //get loan
        $loans = LoansModel::where('loan_status', 'ACTIVE')->get();

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
            //}
        }

        return response([
            'success' => true
        ]);
    }

    public function loan_repayment(Request $request)
    {
        //go to loan repayment table and pick confirmed but not posted loans
        $repayment = LoanRepaymentModel::where('status', 'confirmed')->where('posted', false)->get();
        dd($repayment);
    }


    public function request(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'processing')->where('loan_officer_approval', false)->get();

        return view('loans.requests')->with(['loans' => $loans]);
    }

    public function under_processing(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'processing')->where('loan_officer_approval', true)->get();
        return view('loans.under_processing')->with(['loans' => $loans]);
    }

    public function request_card(Request $request, $id)
    {
        //get loan details
        $loan = LoansModel::where('id', $id)->first();
        //get customer details
        $customer = Customer::where('id', $loan->customer_id)->first();
        $identity = false;
        $photo = false;
        $form = false;
        $guarantor = false;
        $agreement = false;
        $loan_forms = LoanForm::where('loan_id', $id)->get();
        foreach ($loan_forms as $item) {
            if ($item->title == 'ID Number') {
                $identity = true;
            }

            if ($item->title == 'Photo') {
                $photo = true;
            }

            if ($item->title == 'Loan Form') {
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
        $security = LoanSecurityType::where('active', true)->get();
        //calculate payments
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();
        foreach ($deduction as $item) {
            $rec = array();
            $rec['name'] = $item->name;
            if ($item->percentange == true) {
                $rec['amount'] = $loan->loan_amount * ($item->percentange_amount / 100);
            } else {
                $rec['amount'] = $item->amount;
            }
            $deductions[] = $rec;
        }

        $data = DB::select("select sum(amount) as balance from payments where customer_id = '" . $customer->id . "' LIMIT 1;");

        return view('loans.request_card')
            ->with([
                'loan' => $loan,
                'deductions' => $deductions,
                'securities' => $security,
                'customer' => $customer,
                'loan_forms' => $loan_forms,
                'identity' => $identity,
                'photo' => $photo,
                'guarantor' => $guarantor,
                'agreement' => $agreement,
                'form' => $form,
                'customer_savings' => $data[0]->balance
            ]);
    }

    public function processing_loan_card(Request $request, $id)
    {
        //get loan details
        $loan = LoansModel::where('id', $id)->first();
        //get customer details
        $customer = Customer::where('id', $loan->customer_id)->first();
        $identity = false;
        $photo = false;
        $form = false;
        $guarantor = false;
        $agreement = false;
        $loan_forms = LoanForm::where('loan_id', $id)->get();

        foreach ($loan_forms as $item) {
            if ($item->title == 'ID Number') {
                $identity = true;
            }

            if ($item->title == 'Photo') {
                $photo = true;
            }

            if ($item->title == 'Loan Form') {
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
                $rec['amount'] = $loan->loan_amount * ($item->percentange_amount / 100);
            } else {
                $rec['amount'] = $item->amount;
            }
            $deductions[] = $rec;
        }

        $savings = Payments::where('customer_id', $customer->id)->where('status', 'confirmed')->sum('amount');

        return view('loans.processing_loan_card')
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
                'loan_forms' => $loan_forms,
                'savings' => $savings
            ]);
    }

    public function download_file(Request $request)
    {
        if (Storage::disk('loan_docs')->exists(""));
    }

    public function loan_officer_approval(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        if ($request->approval == 0) {
            $ln = LoansModel::where('id', $id)->update([
                'direct' => true,
                'loan_officer_approval' => true,
                'loan_officer_remarks' => $request->comment
            ]);
        } else if ($request->approval == 1) {
            $ln = LoansModel::where('id', $id)->update([
                'legal' => true,
                'loan_officer_approval' => true,
                'loan_officer_remarks' => $request->comment
            ]);
        } else if ($request->approval == 2) {
            $ln = LoansModel::where('id', $id)->update([
                'public_finance' => true,
                'loan_officer_approval' => true,
                'loan_officer_remarks' => $request->comment
            ]);
        } else {
        }
        return redirect()->to('/under_processing');
    }

    public function ImportLoans(Request $request)
    {
        Excel::import(new AccImport, $request->file);
        return "done";
    }

    public function loans_by_branch(Request $request)
    {
        $branches = Branch::all();
        if ($request->branch == null) {
            $loans = LoansModel::where('branch', 'Asaba')->get();
        } else {
            $loans = LoansModel::where('branch', $request->branch)->get();
        }
        return view('loans.branch')->with(['loans' => $loans, 'branches' => $branches]);
    }

    public function loan_repay_ledger(Request $request)
    {
        try {
            //get loan 
            $loans = LoanRepayment::where('status', 'confirmed')->get();
            $cc = 0;
            foreach ($loans as $item) {
                $ll = LoansModel::where('customer', $item->name)->first();
                if ($ll != null) {
                    $customer = Customer::where('name', $item->name)->first();
                    // LoanLedgerEntries::create([
                    //     'loan_model_id' => $ll->id,
                    //     'customer_id' => $ll->customer_id,
                    //     'customer' => $ll->customer,
                    //     'handler' => $customer->handler,
                    //     'branch' => $customer->branch,
                    //     'remarks' => 'Loan Repayment',
                    //     'debit' => 0,
                    //     'credit' => $item->amount,
                    //     'amount' => $item->amount * -1,
                    //     'created_at' => $item->created_at
                    // ]);
                    $cap = 0;
                    $int = 0;
                    //check if int if full
                    if ($item->monthly_interest <= $item->monthly_interest_paid) {
                        $cap = $item->amount;
                    } else {
                        //get int balance
                        $int_bal = $item->monthly_interest - $item->monthly_interest_paid;
                        if ($item->amount < $int_bal) {
                            $int = $item->amount;
                        } else {
                            $int = $item->amount - $int_bal;
                            $cap = $item->amount - $int;
                        }
                    }

                    // //update_monthly interest
                    $dt = LoansModel::where('id', $item->id)->update([
                        'total_balance' => $item->total_balance - $item->amount,
                        'total_interest_paid' =>  $int,
                        'monthly_principle_paid' => $item->monthly_principle_paid + $cap,
                        'monthly_interest_paid' => $item->monthly_interest_paid + $int,
                        'total_amount_paid' => $item->total_amount_paid + $item->amount,
                        'total_monthly_paid' => ($item->monthly_principle_paid - $cap) + ($item->monthly_interest_paid - $int),
                        'next_charge_date' => Carbon::now()->addMonth()
                    ]);
                }
            }
        } catch (Error $e) {
            Log::error($e);
        }

        return response([
            'success' => true,
            'data' => $cc
        ]);
    }

    public function loan_repay_ledger_single(Request $request, $id)
    {
        //get loan 
        $lons = LoansModel::where('loan_status', 'ACTIVE')->where('customer', 'MR BARTHOLOMEW EMMANUEL')->get();

        foreach ($lons as $ln) {
            $items = LoanRepayment::where('status', 'confirmed')->where('name', $ln->customer)->get();
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $ll = LoansModel::where('customer', $item->name)->first();
                    if ($ll != null) {
                        $customer = Customer::where('name', $item->name)->first();
                        $ll = LoansModel::where('customer', $item->name)->first();
                        $cap = 0;
                        $int = 0;
                        //check if int if full

                        if ($item->amount < ($ln->monthly_interest - $ln->monthly_interest_paid)) {
                            $int = $item->amount;
                        } else {
                            //get int balance
                            $int_bal = $ln->monthly_interest - $ln->monthly_interest_paid;

                            if ($item->amount < $int_bal) {
                                $int = $item->amount;
                            } else {
                                $int = $item->amount - $int_bal;
                                $cap = $item->amount - $int;
                            }
                        }
                        if ($int != 0) {
                            LoanLedgerEntries::create([
                                'loan_model_id' => $ll->id,
                                'customer_id' => $ll->customer_id,
                                'customer' => $ll->customer,
                                'handler' => $customer->handler,
                                'branch' => $customer->branch,
                                'remarks' => 'Interest Repayment',
                                'debit' => 0,
                                'credit' => $cap,
                                'amount' => $cap * -1,
                                'created_at' => $item->created_at
                            ]);
                        }

                        if ($cap != 0) {
                            LoanLedgerEntries::create([
                                'loan_model_id' => $ll->id,
                                'customer_id' => $ll->customer_id,
                                'customer' => $ll->customer,
                                'handler' => $customer->handler,
                                'branch' => $customer->branch,
                                'remarks' => 'Capital Repayment',
                                'debit' => 0,
                                'credit' => $int,
                                'amount' => $int * -1,
                                'created_at' => $item->created_at
                            ]);
                        }

                        $ln->total_balance = $ln->total_balance - $item->amount;
                        $ln->total_interest_paid =  $ln->total_interest_paid + $cap;
                        $ln->monthly_interest_paid = $ln->monthly_interest_paid + $cap;
                        $ln->monthly_principle_paid = $ln->monthly_principle_paid + $int;
                        $ln->total_amount_paid = $ln->total_amount_paid + $item->amount;
                        $ln->total_monthly_paid = $ln->total_monthly_paid + $item->amount;
                        $ln->total_monthly_balance = $ln->total_monthly_balance - $item->amount;
                        $ln->next_charge_date = Carbon::now()->addMonth();
                        $ln->update();
                    }
                }
            }
        }
        return response([
            'success' => true
        ]);
    }

    public function loan_ledger(Request $request)
    {
        $loans = LoansModel::all();
        foreach ($loans as $item) {
            $customer = Customer::where('name', $item->customer)->first();

            LoanLedgerEntries::create([
                'loan_model_id' => $item->id,
                'customer_id' => $customer->id,
                'customer' => $customer->name,
                'handler' => $customer->handler,
                'branch' => $customer->branch,
                'remarks' => 'Opening Balance',
                'debit' => $item->total_balance,
                'credit' => 0,
                'amount' => $item->total_balance,
                'created_at' => $item->created_at
            ]);
        }

        return response([
            'success' => true
        ]);
    }

    public function charge_date(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'ACTIVE')->get();

        foreach ($loans as $item) {
            if ($item->next_charge_date == null) {
                $start_date = $item->start_date;
                $day = Carbon::parse($start_date)->format('d');
                $new_date = "10/" . $day . "/2022";
                $time = strtotime($new_date);
                $newformat = date('Y-m-d', $time);

                $new_ln = LoansModel::where('id', $item->id)->update([
                    'next_charge_date' => $newformat
                ]);
            }
        }
        return response([
            'success' => true
        ]);
    }

    public function active_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Active')->get();
        foreach ($loans as $ln) {
            $now = Carbon::now();
            $diff =  Carbon::parse($now)->diffInDays($ln->exit_date);
            if ($ln->exit_date < $now) {
                $ln->countdown =  $diff * -1;
            } else {
                $ln->countdown = $diff;
            }
        }
        return view('loans.active')->with(['loans' => $loans]);
    }

    public function expired_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Expired')->get();
        foreach ($loans as $ln) {
            $now = Carbon::now();
            $diff =  Carbon::parse($now)->diffInDays($ln->exit_date);
            if ($ln->exit_date < $now) {
                $ln->countdown =  $diff * -1;
            } else {
                $ln->countdown = $diff;
            }
        }

        return view('loans.expired')->with(['loans' => $loans]);
    }

    public function bad_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Bad')->get();
        foreach ($loans as $ln) {
            $now = Carbon::now();
            $diff =  Carbon::parse($now)->diffInDays($ln->exit_date);
            if ($ln->exit_date < $now) {
                $ln->countdown =  $diff * -1;
            } else {
                $ln->countdown = $diff;
            }
        }
        return view('loans.bad')->with(['loans' => $loans]);
    }

    public function loan_card(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $customer = Customer::where('id', $loan->customer_id)->first();
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();

        foreach ($deduction as $item) {
            $rec = array();
            $rec['name'] = $item->name;
            if ($item->percentange == true) {
                $rec['amount'] = $loan->loan_amount * ($item->percentange_amount / 100);
            } else {
                $rec['amount'] = $item->amount;
            }
            $deductions[] = $rec;
        }
        $payments = LoanRepayment::where('name', $loan->customer)->get();
        return view('loans.loan_card')->with([
            'loan' => $loan,
            'payments' => $payments,
            'customer' => $customer,
            'deductions' => $deductions
        ]);
    }

    public function closed_loans(Request $request)
    {
        $payment = LoanRepayment::where('id', '323')->first();
        $loan = LoansModel::where('customer', $payment->name)->first();
        return view('');
    }

    public function repay_test(Request $request, $id)
    {
        $payments = LoanRepayment::where('posted', false)->get();
        foreach ($payments as $payment) {
            $loan = LoansModel::where('customer', $payment->name)->first();

            //get interests
            $loan->total_balance = $loan->total_balance - $payment->amount;
            $loan->total_amount_paid = $loan->total_amount_paid + $payment->amount;
            $loan->total_monthly_paid = $loan->total_monthly_paid + $payment->amount;
            $loan->total_monthly_balance = $loan->total_monthly_balance - $payment->amount;

            //calculate interest and capital repayments
            $expected_interest = $loan->monthly_interest - $loan->monthly_interest_paid;

            if ($expected_interest > $payment->amount) {
                $loan->monthly_interest_paid = $loan->monthly_interest_paid + $payment->amount;
                $loan->total_interest_paid = $loan->total_interest_paid + $payment->amount;
            } else {
                $loan->monthly_interest_paid = $loan->monthly_interest_paid + $expected_interest;
                $loan->total_interest_paid = $loan->total_interest_paid + $expected_interest;

                $rem_capital = $payment->amount - $expected_interest;
                $loan->monthly_principle_paid =  $loan->monthly_principle_paid + $rem_capital;
                $loan->capital_balance = $loan->capital_balance + $rem_capital;
            }

            $loan->update();
            $payment->posted = true;
            $payment->update();
        }

        return response([
            "success" => true
        ]);
    }

    public function loan_status_summary(Request $request)
    {
        if (auth()->user()->loan_officer == false) {
            return abort(401);
        }
        $data = array();
        $data['active'] = LoansModel::where('loan_status', 'ACTIVE')->count();
        $data['expired'] = LoansModel::where('loan_status', 'EXPIRED')->count();
        $data['bad'] = LoansModel::where('loan_status', 'BAD')->count();
        $data['active_amount'] = LoansModel::where('loan_status', 'ACTIVE')->sum('total_balance');
        $data['expired_amount'] = LoansModel::where('loan_status', 'EXPIRED')->sum('total_balance');
        $data['bad_amount'] = LoansModel::where('loan_status', 'BAD')->sum('total_balance');

        return view('loans.loan_status_summary')->with(['data' => $data]);
    }

    public function loan_by_sep(Request $request)
    {
        if ($request->name != null) {
            $loans = LoansModel::where('handler', $request->name)->get();
            foreach ($loans as $ln) {
                $now = Carbon::now();
                $diff =  Carbon::parse($now)->diffInDays($ln->exit_date);
                if ($ln->exit_date < $now) {
                    $ln->countdown =  $diff * -1;
                } else {
                    $ln->countdown = $diff;
                }
            }
        } else {
            $loans = [];
        }
        $seps = User::where('sales_executive', true)->get();
        return view('loans.loans_by_sep')->with(['loans' => $loans, 'seps' => $seps]);
    }

    public function loan_review(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $customer = Customer::where('id', $loan->customer_id)->first();
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();

        foreach ($deduction as $item) {
            $rec = array();
            $rec['name'] = $item->name;
            if ($item->percentange == true) {
                $rec['amount'] = $loan->loan_amount * ($item->percentange_amount / 100);
            } else {
                $rec['amount'] = $item->amount;
            }
            $deductions[] = $rec;
        }
        $previous = LoanReview::where('loan_id', $loan->id)->get();
        $payments = LoanRepayment::where('name', $loan->customer)->get();

        $savings = Payments::where('customer_id', $customer->id)->where('status', 'confirmed')->sum('amount');

        return view('loans.review')->with([
            'loan' => $loan,
            'payments' => $payments,
            'customer' => $customer,
            'deductions' => $deductions,
            'previous' => $previous,
            'savings' => $savings
        ]);
    }

    public function save_review(Request $request)
    {
        $loans_review = LoanReview::create([
            'loan_id' => $request->id,
            'commulative_remarks' => $request->comment,
            'action_plan' => $request->action_plan
        ]);

        return redirect()->to('/loan_review/' . $request->id);
    }

    public function change_status(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $customer = Customer::where('id', $loan->customer_id)->first();
        return view('loans.change_status')->with([
            'loan' => $loan,
            'customer' => $customer
        ]);
    }

    public function post_change_status(Request $request, $id)
    {
        $stop = false;
        if ($request->stop_interest == 'stop') {
            $stop = true;
        } else {
        }

        $loan = LoansModel::where('id', $id)->update([
            'stop_interest' => $stop,
            'status_change_remarks' => $request->remarks,
            'loan_status' => $request->status,
            'status_change_date' => Carbon::now(),
            'changed_by' => auth()->user()->name
        ]);

        return redirect()->to('/loan_card/' . $id);
    }

    public function loan_closure(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $customer = Customer::where('id', $loan->customer_id)->first();

        $payments = LoanRepayment::where('name', $loan->customer)->get();
        return view('loans.closure')->with([
            'loan' => $loan,
            'customer' => $customer,
            'payments' => $payments
        ]);
    }

    public function close_loan(Request $request, $id)
    {
        if ($request->type = "Normal") {
            $normal = true;
        } else {
            $normal = false;
        }
        $loan = LoansModel::where('id', $id)->update([
            'closed' => true,
            'close_remarks' => $request->remarks,
            'closed_by' => auth()->user()->name,
            'stop_interest' => true,
            'normal_close' => $normal,
            'loan_status' => 'CLOSED'
        ]);

        if ($request->amount > 0) {
            $loan = LoansModel::where('id', $id)->first();
            $acc = SavingsAccount::where('customer_id', $loan->customer_id)->first();

            //remove from interest            
            $loan->total_interest_paid = $loan->total_interest_paid - $request->amount;
            $loan->update();

            $reference = rand(100000000, 999999999);
            //add to savings
            $payment = Payments::create([
                'savings_account_id' => $acc->id,
                'plan' => $acc->plan,
                'customer_id' => $acc->customer_id,
                'customer_name' => $acc->customer,
                'transaction_type' => 'savings',
                'status' => 'pending',
                'remarks' => 'Excess Loan Repayment moved to savings',
                'debit' => $request->amount,
                'credit' => 0,
                'amount' => $request->amount,
                'requires_approval' => false,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $reference,
                'reference' => $reference
            ]);
        }
        return redirect()->to('/loan_card/' . $id);
    }
}
