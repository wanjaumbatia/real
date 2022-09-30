<?php

namespace App\Http\Controllers;

use App\Models\Balances;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\NewBalances;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
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

    public function loans(Request $request)
    {
        if (auth()->user()->branch_manager == true) {
            $branch = auth()->user()->branch;


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
                    from loans inner join users on  loans.handler = users.name where branch='" . $branch . "';
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
                    from loans inner join users on  loans.handler = users.name where branch='" . $branch . "' and status = '" . $request->status . "';
                ");
            }


            return view('branch.loans')->with(['loans' => $data, 'status' => $request->status, 'branch' => $branch]);
        } else {
            return abort(401);
        }
    }

    public function loan_card($id)
    {
        //get loan details
        $loan = Loan::where('id', $id)->first();
        //get customer details
        $customer = Customer::where('id', $loan->customer_id)->first();

        //create charges

        //calculate payments

        return view('branch.loan_card')->with(['loan' => $loan, 'customer' => $customer]);
    }

    public function upload_forms($id)
    {
        $url = '/branch_loan/' . $id;
        redirect()->to($url);
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
}
