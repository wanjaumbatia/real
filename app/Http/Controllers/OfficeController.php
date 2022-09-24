<?php

namespace App\Http\Controllers;

use App\Mail\Shortage;
use App\Models\Balances;
use App\Models\CommissionLines;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use App\Models\ShortageLine;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Withdrawal;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='".auth()->user()->branch."';");
        
        // if (auth()->user()->office_admin = true) {
        //     $seps = User::where('sales_executive', true)->where('branch', auth()->user()->branch)->get('name');
        //     $result = array();
        //     foreach ($seps as $sep) {
        //         $val = array();
        //         $val['sep'] = $sep->name;
        //         $expected = Payments::where('status', 'pending')->where('reconciled', false)->where('transaction_type', 'savings')->where('created_by', $sep->name)->sum('debit') -  Payments::where("remarks", "POF")->where('created_by', $sep->name)->where("status", "confirmed")->where('transaction_type', 'withdrawal')->sum("credit");;
        //         $savings = Payments::where('status', 'pending')->where('reconciled', false)->where('transaction_type', 'savings')->where('created_by', $sep->name)->sum('debit');
        //         $regfee = Payments::where('status', 'pending')->where('reconciled', false)->where('transaction_type', 'registration')->where('created_by', $sep->name)->sum('debit');
        //         $withdrawal = Payments::where('status', 'pending')->where('reconciled', false)->where('transaction_type', 'withdrawal')->where('created_by', $sep->name)->sum('credit') -  Payments::where("remarks", "POF")->where('created_by', $sep->name)->where("status", "pending")->where('transaction_type', 'withdrawal')->sum("credit");
        //         $pof = Payments::where("remarks", "POF")->where('reconciled', false)->where("status", "confirmed")->where('transaction_type', 'withdrawal')->where('created_by', $sep->name)->sum("credit");
        //         $loan_collection = LoanRepayment::where('handler', $sep->name)->where('status', 'pending')->sum('amount');

        //         $unconfirmed_pof = Payments::where("remarks", "POF")->where('reconciled', false)->where("status", "pending")->where('transaction_type', 'withdrawal')->where('created_by', $sep->name)->sum("credit");
        //         $val['savings'] = $savings + $regfee + $loan_collection;
        //         $val['expected'] = $val['savings'] - $pof;
        //         $val['withdrawals'] = $withdrawal;
        //         $val['pof'] = $pof;
        //         $val['unconfirmed_pof'] = $unconfirmed_pof;
        //         //$val['loans'] = $loan_tot
        //         $result[] = $val;
        //     }

        //     $total_savings = Payments::where('status', 'pending')->where('transaction_type', 'savings')->orWhere('transaction_type', 'registration')->sum('debit');
        //     $total_withdrawals = Payments::where('status', 'pending')->where('transaction_type', 'savings')->sum('debit');
        //     return view('office.index')->with(['data' => $result, 'total_savings' => 0, 'total_withdrawals' => 0,]);
        // } else {
        //     return abort(401);
        // }
        return view('office.index')->with(['data' => $data,]);
    }

    public function pay_on_field($id)
    {
        $transactions = Payments::where("remarks", "POF")->where('transaction_type', 'withdrawal')->where('created_by', $id)->get();

        return view('office.pof')->with(["data" => $transactions]);
    }

    public function cps_withdrawal(Request $request)
    {
        $transactions = Payments::where("cps", true)->where("status", "pending")->where('transaction_type', 'withdrawal')->get();

        return view('office.cps')->with(["data" => $transactions]);
    }

    public function receive(Request $request)
    {
        $handler = $request->handler;
        $amount = $request->amount;
        $whole = 0;
        $total_loans = LoanRepayment::where('handler', $handler)->where('status', 'pending')->sum('amount');

        $pof = Payments::where("remarks", "POF")->where('reconciled', false)->where("status", "confirmed")->where('transaction_type', 'withdrawal')->where('created_by', $handler)->sum("credit");
        $transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->orWhere('transaction_type', 'registration')->where('status', 'pending')->get();

        $total_transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->where('status', 'pending')->sum('debit');
        $total_regfee = Payments::where('created_by', $handler)->where('transaction_type', 'registration')->where('status', 'pending')->sum('debit');

        if (($total_transactions + $total_regfee - $pof + $total_loans) < $amount) {
            return back()->withErrors(['You can not reconcile more than the required amount of ₦.' . number_format(($total_transactions + $total_regfee - $pof))]);
        } else if (($total_transactions + $total_regfee - $pof + $total_loans) > $amount) {
            // handle shortages
            $short = ($total_transactions + $total_regfee - $pof) - $amount;
            $reference = rand(100000000, 999999999);
            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                ]);

                //create commission line
                // $commission = 0.0025 * $item->debit;
                // $comm_line = CommissionLines::create([
                //     'handler' => $request->handler,
                //     'amount' => $commission,
                //     'description' => 'Commission for sales worth ₦' . number_format($item->debit, 2) . ' for payment reference ' . $reference,
                //     'batch_number' => $reference,
                //     'payment_id' => $item->id,
                //     'disbursed' => false,
                //     'branch' => auth()->user()->branch,
                //     'approved' => true,
                //     // 'transaction_type'=>'commission'
                // ]);
            }

            $shortage_line = ShortageLine::create([
                'sales_executive' => $handler,
                'expected_amount' => ($total_transactions + $total_regfee - $pof + $total_loans),
                'give_amount' => $amount,
                'short' => ($total_transactions + $total_regfee - $pof + $total_loans) - $amount,
                'reference' => $reference,
                'cleared' => false,
                'office_admin' => auth()->user()->name,
                'description' => 'Shortage of ₦' . number_format((($total_transactions + $total_regfee - $pof + $total_loans) - $amount), 2) . ' from ' . $handler . ' ',
                'reported' => false,
                'resolved' => false,
                'branch' => auth()->user()->branch,
                'remarks' => ""
            ]);

            //send notifications

            $myEmail = [
                'charlez.o@reliancegroup.com.ng',
                'compliance@reliancegroup.com.ng',
                'personnel@reliancegroup.com.ng',
                'lucky.nwaise@reliancegroup.com.ng',
                'it@reliancegroup.com.ng',
                'esther.ugbo@reliancegroup.com.ng',
                'nwaisemoses@reliancegroup.com.ng',
                'christopher.om@reliancegroup.com.ng',
                "wanjaumbatia@gmail.com", 
                'davidonyango7872@gmail.com',
            ];
            Mail::to($myEmail)->send(new Shortage($handler, $short, ($total_transactions + $total_regfee - $pof + $total_loans), 0, auth()->user()->branch, auth()->user()->name));

            var_dump(Mail::failures());
            return redirect()->route('office.list');
        } else {
            //clear sales executive
            $reference = rand(100000000, 999999999);
            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciled' => true,
                    'reference' => $reference
                ]);

                $pof = Payments::where("remarks", "POF")->where("status", "confirmed")->where('transaction_type', 'withdrawal')->where('created_by', $handler)->get();

                foreach ($pof as $item) {
                    Payments::where('id', $item->id)->update([
                        'status' => 'confirmed',
                        'reconciled' => true
                    ]);
                }



                //create commission line
                $comm = CommissionLines::where('batch_number', $item->batch_number)->update([
                    'approved' => true,
                    'approved_by' => auth()->user()->name
                ]);
            }
            $loans = LoanRepayment::where('handler', $handler)->where('status', 'pending')->get();

            foreach ($loans as $item) {
                $comm = LoanRepayment::where('id', $item->id)->update([
                    'status' => 'confirmed',
                ]);
            }

            return redirect()->route('office.list');
        }
    }

    public function reconcile($id)
    {
        $transactions = Payments::where('status', 'pending')->where(function ($q) {
            $q->where('transaction_type', 'savings')
                ->orWhere('transaction_type', 'registration');
        })->where('created_by', $id)->get();
        $registration = Payments::where('status', 'pending')->where('transaction_type', 'registration')->where('created_by', $id)->get();
        $pof = Payments::where('created_by', $id)->where('reconciled', false)->where('transaction_type', 'withdrawal')->where('status', 'confirmed')->where('remarks', 'POF')->get();

        $total = 0;
        $total_regfee = 0;
        foreach ($transactions as $item) {
            $total = $total + $item->amount;
        }
        foreach ($registration as $item) {
            $total_regfee = $total_regfee + $item->amount;
        }
        $loan_collection = LoanRepayment::where('handler', $id)->where('status', 'pending')->sum('amount');
        $loans = LoanRepayment::where('handler', $id)->where('status', 'pending')->get();
        $total_loans = LoanRepayment::where('handler', $id)->where('status', 'pending')->sum('amount');
        $total_collection = $total;
        $total = $total;
        $total_pof = Payments::where('created_by', $id)->where('reconciled', false)->where('transaction_type', 'withdrawal')->where('status', 'confirmed')->where('remarks', 'POF')->sum('credit');

        return view('office.reconcile', ['total_loans' => $total_loans, 'loans' => $loans, 'total_pof' => $total_pof, 'total_collection' => $total_collection, 'transactions' => $transactions, 'handler' => $id, 'total' => $total - $total_pof + $loan_collection, 'pof' => $pof]);
    }

    public function commissions()
    {
        $commissions = CommissionLines::all();
        $total = CommissionLines::where('branch', auth()->user()->branch)->sum('amount');

        return view('office.commissions')->with(['commissions' => $commissions, 'total' => $total]);
    }

    public function withdrawal_list($id)
    {
        if (auth()->user()->office_admin == false) {
            return abort(401);
        }

        $transactions = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $id)->get();
        $total = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $id)->get('credit');;

        return view('office.withdrawal_list', ['transactions' => $transactions, 'handler' => $id, 'total' => $total]);
    }

    public function branch_creation(Request $request)
    {
        $seps = User::where('sales_executive', true)->get();
        foreach ($seps as $item) {
            $customers = Customer::where('handler', $item->name)->get();
            foreach ($customers as $customer) {
                Customer::where('id', $customer->id)->update([
                    'branch' => $item->branch,
                ]);

                $accounts = SavingsAccount::where('customer_id', $customer->id)->get();

                foreach ($accounts as $acc) {
                    SavingsAccount::where('id', $acc->id)->update([
                        'branch' => $item->branch,
                        'handler' => $item->name
                    ]);
                }
            }
        }

        return 'done';
    }

    public function save_loan_repayment(Request $request)
    {
        $loans = Loan::all();
        foreach ($loans as $item) {
            $repayment = LoanRepayment::create([
                'loan_number' => $item->id,
                'no' => $item->no,
                'name' => $item->name,
                'amount' => $item->paid,
                'handler' => $item->handler,
                'branch' => "test",
                'description' => "Opening Balance",
                "status" => "confirmed",
                "confirmed_by" => "Admin",
                'document_number' => 'opening',
                "posted" => true
            ]);
        }

        return 'saved';
    }


    public function import_accounts(Request $request)
    {
        $bal = Balances::all();
        $batch_number = rand(100000000, 999999999);
        $reference = rand(100000000, 999999999);

        foreach ($bal as $item) {
            try {
                if ($item->plan == "Regular") {
                    $plan = Plans::where("name", "Regular")->first();
                    $customer = Customer::where('username', $item->userid)->first();
                    Log::warning($item->userid);
                    //create savings account 
                    $acc = SavingsAccount::create([
                        'customer_id' => $customer->id,
                        'customer_number' => $customer->no,
                        'plans_id' => $plan->id,
                        'name' => 'Regular',
                        'pledge' => 0,
                        'created_by' => auth()->user()->name,
                        'active' => true,
                        'branch' => $customer->branch,
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
                        'debit' => $item->amount,
                        'credit' => 0,
                        'amount' => $item->amount,
                        'requires_approval' => false,
                        'approved' => false,
                        'posted' => false,
                        'created_by' => $customer->handler,
                        'branch' => $customer->branch,
                        'batch_number' => $batch_number,
                        'reference' => $reference
                    ]);
                } else if ($item->plan == "RealSavingGold") {
                    $plan = Plans::where("name", "Real Savings Gold")->first();
                    $customer = Customer::where('username', $item->userid)->first();

                    //create savings account 
                    $acc = SavingsAccount::create([
                        'customer_id' => $customer->id,
                        'customer_number' => $customer->no,
                        'plans_id' => $plan->id,
                        'name' => 'Regular',
                        'pledge' => 0,
                        'created_by' => auth()->user()->name,
                        'active' => true,
                        'branch' => $customer->branch,
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
                        'debit' => $item->amount,
                        'credit' => 0,
                        'amount' => $item->amount,
                        'requires_approval' => false,
                        'approved' => false,
                        'posted' => false,
                        'created_by' => $customer->handler,
                        'branch' => $customer->branch,
                        'batch_number' => $batch_number,
                        'reference' => $reference
                    ]);
                } else if ($item->plan == "RealSavingDiamond") {
                    $plan = Plans::where("name", "Real Savings Diamond")->first();
                    $customer = Customer::where('username', $item->userid)->first();

                    //create savings account 
                    $acc = SavingsAccount::create([
                        'customer_id' => $customer->id,
                        'customer_number' => $customer->no,
                        'plans_id' => $plan->id,
                        'name' => 'Regular',
                        'pledge' => 0,
                        'created_by' => auth()->user()->name,
                        'active' => true,
                        'branch' => $customer->branch,
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
                        'debit' => $item->amount,
                        'credit' => 0,
                        'amount' => $item->amount,
                        'requires_approval' => false,
                        'approved' => false,
                        'posted' => false,
                        'created_by' => $customer->handler,
                        'branch' => $customer->branch,
                        'batch_number' => $batch_number,
                        'reference' => $reference
                    ]);
                } else if ($item->plan == "RealSavingDiamond") {
                    $plan = Plans::where("name", "Real Savings Platinum")->first();
                    $customer = Customer::where('username', $item->userid)->first();

                    //create savings account 
                    $acc = SavingsAccount::create([
                        'customer_id' => $customer->id,
                        'customer_number' => $customer->no,
                        'plans_id' => $plan->id,
                        'name' => 'Regular',
                        'pledge' => 0,
                        'created_by' => auth()->user()->name,
                        'active' => true,
                        'branch' => $customer->branch,
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
                        'debit' => $item->amount,
                        'credit' => 0,
                        'amount' => $item->amount,
                        'requires_approval' => false,
                        'approved' => false,
                        'posted' => false,
                        'created_by' => $customer->handler,
                        'branch' => $customer->branch,
                        'batch_number' => $batch_number,
                        'reference' => $reference
                    ]);
                } else if ($item->plan == "RealChristmas") {
                    $plan = Plans::where("name", "Real Christmas")->first();
                    $customer = Customer::where('username', $item->userid)->first();

                    //create savings account 
                    $acc = SavingsAccount::create([
                        'customer_id' => $customer->id,
                        'customer_number' => $customer->no,
                        'plans_id' => $plan->id,
                        'name' => 'Regular',
                        'pledge' => 0,
                        'created_by' => auth()->user()->name,
                        'active' => true,
                        'branch' => $customer->branch,
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
                        'debit' => $item->amount,
                        'credit' => 0,
                        'amount' => $item->amount,
                        'requires_approval' => false,
                        'approved' => false,
                        'posted' => false,
                        'created_by' => $customer->handler,
                        'branch' => $customer->branch,
                        'batch_number' => $batch_number,
                        'reference' => $reference
                    ]);
                }
            } catch (Throwable $e) {
                Log::warning($e);
            }
        }


        return "done";
    }

    public function recon_page($id)
    {
        $transaction = Payments::where('status', 'pending')->where('id', $id)->first();

        return view('office.reconcile_withdrawal', ['transaction' => $transaction, 'created_by' => $id]);
    }


    public function disburse(Request $request)
    {
        Payments::where('id', $request->id)->update([
            'status' => 'confirmed',
        ]);

        $tt = Payments::where('id', $request->id)->first();

        $tt1 = Payments::where('batch_number', $tt->batch_number)->update([
            'status' => 'confirmed',
        ]);


        $tt = CommissionLines::where('batch_number', $request->batch_number)->update([
            'approved' => true,
        ]);

        $transaction = Withdrawal::where('status', 'pending')->where('id', $request->id)->first();

        return redirect()->route('office.list',);
    }


    public function balance($name)
    {
        $savings = Payments::where('status', 'pending')->where('transaction_type', 'savings')->where('created_by', $name)->sum('debit');
        $regfee = Payments::where('status', 'pending')->where('transaction_type', 'registration')->where('created_by', $name)->sum('debit');
        $withdrawal = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $name)->sum('credit');
    }
}
