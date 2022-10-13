<?php

namespace App\Http\Controllers;

use App\Models\Balances;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDeduction;
use App\Models\LoanForm;
use App\Models\LoanRepayment;
use App\Models\LoanSecurityType;
use App\Models\LoansModel;
use App\Models\NewBalances;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use App\Models\User;
use Error;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class BranchController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '12000');
        ini_set('request_terminate_time', '12000');
    }

    public function all_clients(Request $request)
    {
        //$customers = //->get();
        $customers = Cache::remember('customers', 120, function () {
            return Customer::where('branch', auth()->user()->branch)->paginate(20000); //->get('id', 'name', 'phone', 'address', 'hanlder');
        });
        return view('branch.all_clients')->with('customers', $customers);
    }

    public function sales_executives(Request $request)
    {
        if (auth()->user()->branch_manager == false) {
            return abort(401);
        } else {
            $seps = User::where('sales_executive', true)->where('branch', auth()->user()->branch)->get();
            return view('branch.seps')->with(['seps' => $seps]);
        }
    }
    public function customer($id)
    {
        if (auth()->user()->branch_manager == false) {
            return abort(401);
        } else {
            //get customer details
            $customer = Customer::where('handler', auth()->user()->name)->where('id', $id)->first();

            //get customer accounts
            $customer = Customer::where('id', $id)->first();
            $plans = Plans::where('active', true)->get();
            //get accounts
            $accounts = SavingsAccount::where('customer_id', $customer->id)->orderBy('id', 'ASC')->where('active', true)->get();

            $data = array();
            $total_balance = 0;
            foreach ($accounts as $acc) {
                $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
                $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'savings')->where('status', 'pending')->sum('amount');
                $pending_withdrawal = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('amount');
                $pending_penalty = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'penalty')->where('status', 'pending')->sum('amount');
                $plan = Plans::where('id', $acc->plans_id)->first();
                $saving_accounts = array();
                $saving_accounts['details'] = $acc;
                $saving_accounts['plan'] = $plan;
                $saving_accounts['confirmed'] = number_format($confirmed_transaction, 2);
                $saving_accounts['pending_withdrawal'] = number_format(($pending_withdrawal + $pending_penalty) * -1, 2);
                if ($acc->plan == "Regular") {
                    $loan = Loan::where('customer_id', $customer->id)->first();
                    if ($loan != null) {
                        $pend_loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'pending')->sum('amount');
                        $pending_transaction = $pending_transaction + $pend_loan_repayment;
                    }
                }
                $saving_accounts['pending'] = number_format($pending_transaction, 2);
                $data[] = $saving_accounts;
                $total_balance = $total_balance + $confirmed_transaction;
            }

            $loan = LoansModel::where('customer_id', $customer->id)->first();


            $result = array();
            $result['customer'] = $customer;
            $result['accounts'] = $data;
            $result['plans'] = $plans;
            $result['loan'] = $loan;
            $result['total_balance'] = $total_balance;

            $plans = Plans::all();

            return view('branch.customer')->with(['customer' => $customer, 'result' => $result, 'plans' => $plans]);
        }
    }

    public function loans(Request $request)
    {
        if (auth()->user()->branch_manager == true) {
            $branch = auth()->user()->branch;


            $loans = LoansModel::where('branch', auth()->user()->branch)->get();

            return view('branch.loans')->with(['loans' => $loans, 'status' => $request->status, 'branch' => $branch]);
        } else {
            return abort(401);
        }
    }

    public function pending_branch_loans(Request $request)
    {
        $branch = auth()->user()->branch;
        // $data = DB::select("
        // select 
        //         loans.id, 
        //         loans.name, 
        //         loans.application_date,
        //         loans.customer_id,
        //         loans.amount, 
        //         loans.paid,
        //         loans.interest_percentage,
        //         loans.duration,
        //         loans.handler,
        //         loans.status,
        //         loans.remarks,
        //         users.branch,
        //         loans.current_savings
        //     from loans inner join users on  loans.handler = users.name where branch='" . $branch . "' and loans.status ='pending';
        // ");

        $data = LoansModel::where('loan_status', 'pending')->where('branch', $branch)->get();


        return view('branch.applied_loans')->with(['loans' => $data,]);
    }

    public function processing_branch_loans(Request $request)
    {
        $branch = auth()->user()->branch;

        $data = LoansModel::where('branch', auth()->user()->branch)->where('loan_status', 'processing')->get();

        return view('branch.processing_loans')->with(['loans' => $data,]);
    }

    public function approved_branch_loans(Request $request)
    {
        $branch = auth()->user()->branch;
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
            from loans inner join users on  loans.handler = users.name where branch='" . $branch . "' and loans.status ='approved';
        ");

        return view('branch.approved_loans')->with(['loans' => $data,]);
    }

    public function save_security(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        if ($request->Collateral) {
            $ln = Loan::where('id', $id)->update([
                'legal' => true,
                'Collateral' => true
            ]);
        }

        if ($request->Guarantorship) {
            $ln = Loan::where('id', $id)->update([
                'direct' => true,
                'Guarantorship' => true
            ]);
        }

        if ($request->CivilServantGuarantee) {
            $ln = Loan::where('id', $id)->update([
                'public_finance' => true,
                'CivilServantGuarantee' => true
            ]);
        }

        if ($request->Cheque) {
            $ln = Loan::where('id', $id)->update([
                'direct' => true,
                'Cheque' => true
            ]);
        }

        $url = '/branch_loan/' . $id;

        return redirect()->to($url);
    }

    public function loan_card($id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $customer = Customer::where('id', $loan->customer_id)->first();
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();
        if ($loan->branch != auth()->user()->branch) {
            return abort(401);
        }
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
        return view('branch.loan_card')->with([
            'loan' => $loan,
            'payments' => $payments,
            'customer' => $customer,
            'deductions' => $deductions
        ]);
    }

    public function upload_forms(Request $request, $id)
    {
        if ($request->file('id_card') != null) {
            $id_url = $request->file('id_card')->store('loan_docs', 'public');

            $form = LoanForm::create([
                'url' => $id_url,
                'title' => 'ID Number',
                'loan_id' => $id
            ]);
        }

        if ($request->file('passport_photo') != null) {
            $id_url = $request->file('passport_photo')->store('loan_docs', 'public');
            $form = LoanForm::create([
                'url' => $id_url,
                'title' => 'Photo',
                'loan_id' => $id
            ]);
        }

        if ($request->file('loan_form') != null) {
            $id_url = $request->file('loan_form')->store('loan_docs', 'public');
            $form = LoanForm::create([
                'url' => $id_url,
                'title' => 'Loan Form',
                'loan_id' => $id
            ]);
        }

        if ($request->file('guarantor_form') != null) {
            $id_url = $request->file('guarantor_form')->store('loan_docs', 'public');
            $form = LoanForm::create([
                'url' => $id_url,
                'title' => 'Guarantor',
                'loan_id' => $id
            ]);
        }

        if ($request->file('agreement_form') != null) {
            $id_url = $request->file('agreement_form')->store('loan_docs', 'public');
            $form = LoanForm::create([
                'url' => $id_url,
                'title' => 'Agreement',
                'loan_id' => $id
            ]);
        }

        $url = '/branch_loan/' . $id;

        return redirect()->to($url);
    }


    public function branch_approve_loan(Request $request, $id)
    {
        $loan = LoansModel::where('id', $id)->first();
        $ln = LoansModel::where('id', $id)->update([
            'loan_status' => 'processing',
            'branch_manager_approval' => true,
            'branch_manager_remarks' => $request->comment
        ]);

        return redirect()->to('/branch_loans');
    }

    public function branch_reject_loan(Request $request, $id)
    {
        $loan = Loan::where('id', $id)->first();
        $ln = Loan::where('id', $id)->update([
            'status' => 'rejected',
            'branch_manager_remarks' => $request->comment
        ]);

        return redirect()->to('/branch_loans');
    }


    public function upload_accounts()
    {
        //get balances
        $balance = NewBalances::all();

        foreach ($balance as $bal) {
            try {
                //check if account exist
                $customer = Customer::where('username', $bal->userid)->first();
                if ($customer != null) {
                    $acc = SavingsAccount::where('customer_id', $customer->id)->get();

                    if (count($acc) == 0) {
                        Log::info($customer);
                        $batch_number = rand(100000000, 999999999);
                        $reference = rand(100000000, 999999999);

                        $user = User::where('name', $customer->handler)->first();
                        if ($bal->plan == "Regular") {

                            $plan = Plans::where("name", "Regular")->first();
                            $customer = Customer::where('username', $bal->userid)->first();
                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => 'Regular',
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name
                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        } else if ($bal->plan == "") {
                            $plan = Plans::where("name", "Regular")->first();
                            $customer = Customer::where('username', $bal->userid)->first();
                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => 'Regular',
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name
                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        } else if ($bal->plan == "RealSavingGold") {
                            $plan = Plans::where("name", "Real Savings Gold")->first();
                            $customer = Customer::where('username', $bal->userid)->first();

                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => $plan->name,
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name

                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        } else if ($bal->plan == "RealSavingDiamond") {
                            $plan = Plans::where("name", "Real Savings Diamond")->first();
                            $customer = Customer::where('username', $bal->userid)->first();

                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => $plan->name,
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name
                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        } else if ($bal->plan == "RealSavingPlatinum") {
                            $plan = Plans::where("name", "Real Savings Platinum")->first();
                            $customer = Customer::where('username', $bal->userid)->first();

                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => $plan->name,
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name
                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        } else if ($bal->plan == "RealChristmas") {
                            $plan = Plans::where("name", "Real Christmas")->first();
                            $customer = Customer::where('username', $bal->userid)->first();

                            //create savings account 
                            $acc = SavingsAccount::create([
                                'customer_id' => $customer->id,
                                'customer_number' => $customer->no,
                                'plans_id' => $plan->id,
                                'name' => $plan->name,
                                'pledge' => 0,
                                'created_by' => 'Admin',
                                'active' => true,
                                'branch' => $user->branch,
                                'handler' => $customer->handler,
                                'customer' => $customer->name,
                                'plan' => $plan->name
                            ]);

                            //create opening balance
                            $payment = Payments::create([
                                'savings_account_id' => $acc->id,
                                'plan' => $acc->plan,
                                'customer_id' => $acc->customer_id,
                                'customer_name' => $acc->customer,
                                'transaction_type' => 'savings',
                                'status' => 'confirmed',
                                'remarks' => 'Opening Balance',
                                'debit' => $bal->amount,
                                'credit' => 0,
                                'amount' => $bal->amount,
                                'requires_approval' => false,
                                'approved' => false,
                                'posted' => false,
                                'created_by' => $customer->handler,
                                'branch' => $user->branch,
                                'batch_number' => $batch_number,
                                'reference' => $reference
                            ]);
                        }

                        return 'success';
                    }
                } else {
                    Log::info($bal);
                }
            } catch (Error $e) {
                Log::info($e);
            }
        }
    }

    public function disburse_loan($id)
    {
        $loan = Loan::where('id', $id)->first();
        $account = SavingsAccount::where('customer_id', $loan->customer_id)->where('plan', 'Regular')->first();

        //get deductions
        $deduction = LoanDeduction::where('active', true)->get();
        $deductions = array();
        $batch_number = rand(100000000, 999999999);
        foreach ($deduction as $item) {
            if ($item->percentange == true) {
                $amount = $loan->amount * ($item->percentange_amount / 100);
                //create charge line
                $charge = Payments::create([
                    'savings_account_id' => $account->id,
                    'plan' => $account->plan,
                    'customer_id' => $account->customer_id,
                    'customer_name' => $account->customer,
                    'transaction_type' => 'charge',
                    'status' => 'confirmed',
                    'remarks' => $item->name . ' Fee - ' . number_format($amount, 2) . ".",
                    'debit' => 0,
                    'credit' => $amount,
                    'amount' => $amount * -1,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => auth()->user()->name,
                    'branch' => auth()->user()->branch,
                    'batch_number' => $batch_number
                ]);
            } else {
                $amount = $item->amount;
                $charge = Payments::create([
                    'savings_account_id' => $account->id,
                    'plan' => $account->plan,
                    'customer_id' => $account->customer_id,
                    'customer_name' => $account->customer,
                    'transaction_type' => 'charge',
                    'status' => 'confirmed',
                    'remarks' => $item->name . ' Fee - ' . number_format($amount, 2) . ".",
                    'debit' => 0,
                    'credit' => $amount,
                    'amount' => $amount * -1,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => auth()->user()->name,
                    'branch' => auth()->user()->branch,
                    'batch_number' => $batch_number
                ]);
            }
        }


        $ln = Loan::where('id', $id)->update([
            'status' => 'running',
        ]);

        return redirect('/branch_loan/' . $loan->id);
    }

    public function processing_loan_card($id)
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


        return view('branch.processing_loan_card')
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
                'form' => $form
            ]);
    }

    public function active_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Active')->where('branch', auth()->user()->branch)->get();
        return view('branch.active')->with(['loans' => $loans]);
    }

    public function expired_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Expired')->where('branch', auth()->user()->branch)->get();
        return view('branch.expired')->with(['loans' => $loans]);
    }

    public function bad_loans(Request $request)
    {
        $loans = LoansModel::where('loan_status', 'Bad')->where('branch', auth()->user()->branch)->get();
        return view('branch.bad')->with(['loans' => $loans]);
    }

    public function loan_status_summary(Request $request)
    {
        if(auth()->user()->branch_manager == false){
            return abort(401);
        }
        $data = array();
        $data['active'] = LoansModel::where('loan_status', 'ACTIVE')->where('branch', auth()->user()->branch)->count();
        $data['expired'] = LoansModel::where('loan_status', 'EXPIRED')->where('branch', auth()->user()->branch)->count();
        $data['bad'] = LoansModel::where('loan_status', 'BAD')->where('branch', auth()->user()->branch)->count();
        $data['active_amount'] = LoansModel::where('loan_status', 'ACTIVE')->where('branch', auth()->user()->branch)->sum('total_balance');
        $data['expired_amount'] = LoansModel::where('loan_status', 'EXPIRED')->where('branch', auth()->user()->branch)->sum('total_balance');
        $data['bad_amount'] = LoansModel::where('loan_status', 'BAD')->where('branch', auth()->user()->branch)->sum('total_balance');

        return view('branch.loan_status_summary')->with(['data' => $data]);
    }
}
