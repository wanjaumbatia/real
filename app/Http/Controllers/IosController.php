<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->sales_executive == false) {
            return abort(401);
        } else {
            $customers = Customer::where('handler', auth()->user()->name)->get();
            return view('ios.index')->with(['customers' => $customers]);
        }
    }

    public function customer($id)
    {
        if (auth()->user()->sales_executive == false) {
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

            $loan = Loan::where('customer_id', $customer->id)->first();
            if ($loan != null) {
                $loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'confirmed')->sum('amount');
                $loan_balance = $loan->amount - $loan_repayment - $loan->paid;
                $principle = $loan->amount / $loan->duration;
                $interest = $loan->amount * ((float)$loan->interest_percentage / 100);
                $loan['monthly_paid'] = $loan_repayment;
                $loan['balance'] = number_format($loan_balance + $interest, 2);
                $loan['monthly_balance'] = number_format($principle + $interest - $loan_repayment, 2);
                $loan['repayment'] = number_format($principle + $interest, 2); // plus 
                $pending_loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'pending')->sum('amount');
                $loan['pending_loan_repayment'] = $pending_loan_repayment;
            }

            $result = array();
            $result['customer'] = $customer;
            $result['accounts'] = $data;
            $result['plans'] = $plans;
            $result['loan'] = $loan;
            $result['total_balance'] = $total_balance;

            $plans = Plans::all();

            return view('ios.customer')->with(['customer' => $customer, 'result' => $result, 'plans' => $plans]);
        }
    }

    public function create_plan(Request $request)
    {
        $plan = Plans::where('id', $request->plan)->first();
        $customer = Customer::where('id', $request->customer)->first();

        $name = $request->name;
        if ($name == null) {
            $name = $plan->name . ".";
        }
        $account = SavingsAccount::create([
            'customer_id' => $customer->id,
            'customer_number' => $customer->no,
            'pledge' => 0,
            'plans_id' => $plan->id,
            'name' => $name,
            'created_by' => "Admin",
            'active' => true,
            'branch' => 'test',
            'handler' => $customer->name,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);

        return redirect()->route('ios.customer', [$customer->id]);
    }

    public function make_payment($id)
    {
        $customer = Customer::where('id', $id)->first();
        $accounts = SavingsAccount::where('customer_id', $id)->orderBy('id', 'ASC')->where('active', true)->get();

        return view('ios.payment')->with(['customer' => $customer, 'accounts' => $accounts]);
    }

    public function make_withdrawal($id)
    {
        $acc = SavingsAccount::where('id', $id)->first();
        $customer = Customer::where('id', $acc->customer_id)->first();
        $plan = Plans::where('id', $acc->plans_id)->first();
        $balance = 100000;

        //get Balance

        return view('ios.withdrawal')->with(['customer' => $customer, 'balance' => $balance, 'account' => $acc]);
    }

    public function withdraw(Request $request)
    {

        dd($request);
    }

    public function pay(Request $request)
    {
        dd($request->request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
