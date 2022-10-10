<?php

namespace App\Http\Controllers;

use App\Models\BankAccounts;
use App\Models\CommissionLines;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanDeduction;
use App\Models\LoanLedgerEntries;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentModel;
use App\Models\LoansModel;
use App\Models\OtpCode;
use App\Models\PaymentLocation;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\ReconciliationRecord;
use App\Models\SavingsAccount;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
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
    public function customers()
    {
        $customers = Customer::where('handler', auth()->user()->name)->get();
        return view('sales.customers')->with(['customers' => $customers]);
    }

    public function new_customer_api(Request $request)
    {
        //check dublicate number
        $phone_check = Customer::where('phone', $request->phone)->get();

        if (count($phone_check) > 0) {
            return response([
                'success' => false,
                'message' => 'Phone number is already in use by another customer'
            ]);
        }


        $customer = Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'gender' => $request->gender,
            'town' => $request->town,
            'email' => $request->email,
            'phone' => $request->phone,
            'posted' => false,
            'no' => get_customer_number(),
            'handler' => $request->user()->name,
            'branch' => $request->user()->branch,
            'business' => $request->business,
            'created_by' => $request->user()->name,
        ]);

        foreach ($request->bank_details as $bank) {
            BankAccounts::create([
                'bank_name' => $bank['name'],
                'bank_account' => $bank['account_number'],
                'bank_branch' => $bank['branch'],
                'created_by' => $request->user()->name,
                'customer_id' => $customer->id
            ]);
        }

        $customer = Customer::where('id', $customer->id)->first();

        //create default account
        $plans = Plans::where('default', true)->where('active', true)->get();

        foreach ($plans as $plan) {
            //create savings account
            $account = SavingsAccount::create([
                'customer_id' => $customer->id,
                'customer_number' => $customer->no,
                'plans_id' => $plan->id,
                'name' => 'Regular',
                'pledge' => 0,
                'created_by' => auth()->user()->name,
                'active' => true,
                'branch' => auth()->user()->branch,
                'handler' => auth()->user()->name,
                'customer' => $customer->name,
                'plan' => $plan->name
            ]);
        }

        //send sms
        $phone = $customer->phone;
        $msg = "Dear " . $customer->name . ". Your registration to Reliance Economic Advancement LTD has been approved. Your unique customer number is " . $customer->no . ".";
        $res = sendSMS($phone, $msg);

        return response([
            'success' => true,
            'data' => $customer
        ]);
    }

    public function new_customer()
    {

        $banks =  [
            '',
            'Access Bank Plc',
            'Citibank Nigeria Limited',
            'Ecobank Nigeria Plc',
            'Fidelity Bank Plc',
            'FIRST BANK NIGERIA LIMITED',
            'First City Monument Bank Plc',
            'Globus Bank Limited',
            'Guaranty Trust Bank Plc',
            'Heritage Banking Company Ltd.',
            'Keystone Bank Limited',
            'Parallex Bank Ltd',
            'Polaris Bank Plc',
            'Premium Trust Bank',
            'Providus Bank',
            'STANBIC IBTC BANK PLC',
            'Standard Chartered Bank Nigeria Ltd.',
            'Sterling Bank Plc',
            'SunTrust Bank Nigeria Limited',
            'Titan Trust Bank Ltd',
            'Union Bank of Nigeria Plc',
            'United Bank For Africa Plc',
            'Unity Bank Plc',
            'Wema Bank Plc',
            'Zenith Bank Plc'
        ];

        return view('sales.new_customer')->with(['banks' => $banks]);
    }

    public function save_customer(Request $request)
    {
        //$phone_check = Customer::where('phone', $request->phone)->get();

        // if (count($phone_check) > 0) {
        //     return back()->withErrors(['Phone number is already registed.']);
        // }

        $customer = Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'gender' => $request->gender,
            'town' => $request->town,
            'email' => $request->email,
            'phone' => $request->phone,
            'posted' => false,
            'no' => get_customer_number(),
            'handler' => $request->user()->name,
            'branch' => $request->user()->branch,
            'business' => $request->business,
            'created_by' => $request->user()->name,
        ]);

        if ($request->bank != null) {
            BankAccounts::create([
                'bank_name' => $request->bank,
                'bank_account' => $request->account,
                'bank_branch' => $request->bank_branch,
                'created_by' => $request->user()->name,
                'customer_id' => $customer->id
            ]);
        }

        $customer = Customer::where('id', $customer->id)->first();

        //create default account
        $plans = Plans::where('default', true)->where('active', true)->get();

        foreach ($plans as $plan) {
            //create savings account
            $account = SavingsAccount::create([
                'customer_id' => $customer->id,
                'customer_number' => $customer->no,
                'plans_id' => $plan->id,
                'name' => 'Regular',
                'pledge' => 0,
                'created_by' => auth()->user()->name,
                'active' => true,
                'branch' => auth()->user()->branch,
                'handler' => auth()->user()->name,
                'customer' => $customer->name,
                'plan' => $plan->name
            ]);
        }

        //send sms
        $phone = $customer->phone;
        $msg = "Dear " . $customer->name . ". Your registration to Reliance Economic Advancement LTD has been approved. Your unique customer number is " . $customer->no . ".";
        $res = sendSMS($phone, $msg);

        return redirect()->to('/customer/' . $customer->id);
    }

    public function loan_card($id)
    {
        $loan = LoansModel::where('id', $id)->first();
        //get customer details
        $customer = Customer::where('id', $loan->customer_id)->first();
        
        $statement = LoanLedgerEntries::where('loan_model_id', $loan->id)->get();   

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

        return view('sales.loan_card')->with([
            'customer' => $customer, 'loan' => $loan, 'statement' => $statement, 'deductions' => $deductions,
        ]);
    }

    public function withdrawal_list()
    {
        $payments = Payments::where('created_by', auth()->user()->name)->where('transaction_type', 'withdrawal')->get();

        return view('sales.withdrawal_list')->with(['data' => $payments]);
    }

    public function show_collection()
    {
        $collections = Payments::where('created_by', auth()->user()->name)->orderBy('created_at', 'DESC')->where('remarks', '!=', 'Opening Balance')->where('status', '!=', 'open')->get();
        return view('sales.payments')->with(['data' => $collections]);
    }

    public function show_recon_group()
    {
        $branch = auth()->user()->branch;

        $recons = Payments::where('created_by', auth()->user()->name)->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
            return $item->created_at->format('d-M-y');
        });

        $result = array();
        foreach ($recons as $item) {
            $deposits = 0;
            $withdrawals = 0;
            $charges = 0;
            $regfees = 0;
            $data = array();
            $data['date'] = $item[0]->created_at->format('d-m-Y');;
            foreach ($item as $it) {
                if ($it->transaction_type == 'savings') {
                    $deposits = $deposits + $it->debit;
                }
                if ($it->transaction_type == 'withdrawal') {
                    $withdrawals = $withdrawals + $it->credit;
                }
                if ($it->transaction_type == 'charge') {
                    $charges = $charges + $it->credit;
                }
                if ($it->transaction_type == 'registration') {
                    $regfees = $regfees + $it->debit;
                }
            }
            $data['deposits'] = $deposits;
            $data['withdrawals'] = $withdrawals;
            $data['charges'] = $charges;
            $data['regfees'] = $regfees;
            $result[] = $data;
        }

        return view('sales.reconciled_group')->with(['data' => $result]);
    }

    public function show_recon($date)
    {
        $collections = ReconciliationRecord::where('handler', auth()->user()->name)->whereDate('created_at', Carbon::parse($date))->orderBy('created_at', 'DESC')->get();
        return view('sales.reconciled')->with(['data' => $collections]);
    }

    public function show_recon_data($reference)
    {
        $payment = Payments::get();

        return view('sales.recon_statement')->with(['data' => $payment]);
    }

    public function collection($id)
    {
        $customer = Customer::where('id', $id)->first();
        //get accounts
        $accounts = SavingsAccount::where('customer_id', $customer->id)->get();
        return view('sales.collection')->with(['customer' => $customer, 'accounts' => $accounts]);
    }

    public function withdrawal($id)
    {
        $account = SavingsAccount::where('id', $id)->first();
        $customer = Customer::where('id', $account->customer_id)->first();
        $plan = Plans::where('id', $account->plans_id)->first();

        return view('sales.withdrawal')->with(['customer' => $customer, 'account' => $account]);
    }

    public function post_withdrawal(Request $request)
    {
        $account = SavingsAccount::where('id', $request->id)->first();
        $balance = Payments::where('savings_account_id', $account->id)->where('status', 'confirmed')->sum('amount');
        Log($balance);
        $customer = Customer::where('id', $account->customer_id)->first();

        $plan = Plans::where('id', $account->plans_id)->first();

        $total_credit = $request->amount + $request->commission;
        $otp = rand(000000, 999999);


        if ($plan->outward == false) {

            Log::info($request);

            if ($balance < $total_credit) {
                return response([
                    "success" => false,
                    "message" => "You do not have enough balance in this account to withdraw ₦" . number_format($request->amount) . "."
                ]);
            }
            //check pending withdrawal
            $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');
            $pending_charge = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'charge')->sum('credit');

            if ($balance < ($total_credit + $pending_withdrawal + $pending_charge)) {
                return response([
                    "success" => false,
                    "message" => "Unable to process this withdrawal since customer has a pending withdrawal, this amount exceed the remaining balance."
                ]);
            }

            $plan = Plans::where('name', $account->plan)->first();

            $expected_commision = $request->amount * $plan->charge;

            $approval = false;
            if ($expected_commision > $request->commission) {
                $approval = true;
            } else {
                $approval = false;
            }

            if ($plan->outward == false) {
                $commision = $request->amount *  $plan->charge;
            }

            $batch_number = rand(100000000, 999999999);
            //create withdrawal line
            $withdrawal = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'withdrawal',
                'status' => 'open',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->amount,
                'amount' => $request->amount * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => auth()->user()->name,
                'branch' => auth()->user()->branch,
                'batch_number' => $otp
            ]);

            //create charge line
            $charge = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'charge',
                'status' => 'open',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->commission, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->commission,
                'amount' => $request->commission * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => auth()->user()->name,
                'branch' => auth()->user()->branch,
                'batch_number' => $otp
            ]);


            $sep_commision = $request->commission * $plan->sep_commission;
            //create sales agent line
            $comm_line = CommissionLines::create([
                'handler' => $account->handler,
                'amount' => $sep_commision,
                'description' => 'Commission for withdrawal of ₦' . number_format($request->amount, 2) . ' charged at ₦' . number_format($request->commission, 2) . ' for ' . $account->customer,
                'batch_number' => $batch_number,
                'transaction_type' => 'withdrawal',
                'payment_id' => $charge->id,
                'disbursed' => false,
                'branch' => auth()->user()->branch,
                'approved' => false,
                // 'transaction_type'=>'commission'
            ]);
        }

        return response([
            "success" => true,
            "code" => 'otp'
        ]);
    }

    public function verify_withdrawal(Request $request)
    {
        $payments = Payments::where('batch_number', $request->otp)->get();
        $reference = rand(100000000, 999999999);
        if (count($payments) > 0) {
            foreach ($payments as $item) {
                if ($request->payment == "Pay On Field") {
                    //handle request approval for disbursement
                    $tt = Payments::where('id', $item->id)->update([
                        'status' => 'pending',
                        'batch_number' => $reference,
                        'remarks' => "POF"
                    ]);
                } else if ($request->payment == "Office Admin") {
                    $tt = Payments::where('id', $item->id)->update([
                        'status' => 'pending',
                        'batch_number' => $reference
                    ]);
                } else if ($request->payment == "Bank Transfer") {
                    $tt = Payments::where('id', $item->id)->update([
                        'status' => 'pending',
                        'batch_number' => $reference,
                        'cps' => true
                    ]);
                }
            }
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal posted successfully'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP Code'
            ]);
        }
    }

    public function withdrawal_fix(Request $request)
    {
        $acc = SavingsAccount::where('id', $request->id)->first();
        if ($acc != null) {
            $item = Payments::where('savings_account_id', $request->id)->first();
            if ($item != null) {
                $payment = Payments::create([
                    'savings_account_id' => $acc->id,
                    'plan' => $acc->plan,
                    'customer_id' => $acc->customer_id,
                    'customer_name' => $acc->customer,
                    'transaction_type' => 'savings',
                    'status' => $item->status,
                    'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'] - 1000, 2),
                    'debit' => $item['amount'] - 1000,
                    'credit' => 0,
                    'amount' => $item['amount'] - 1000,
                    'requires_approval' => false,
                    'approved' => $item->approved,
                    'posted' => false,
                    'created_by' => $item->created_by,
                    'branch' => $item->branch,
                    'batch_number' => $item->batch_number,
                    'reference' => $item->reference
                ]);

                $payment1 = Payments::create([
                    'savings_account_id' => $acc->id,
                    'plan' => $acc->plan,
                    'customer_id' => $acc->customer_id,
                    'customer_name' => $acc->customer,
                    'transaction_type' => 'registration',
                    'status' =>  $item->status,
                    'remarks' => 'Registration Fee from ' . $acc->customer . ' of ₦' . number_format(1000, 2),
                    'debit' => 1000,
                    'credit' => 0,
                    'amount' => 1000,
                    'requires_approval' => false,
                    'approved' =>  $item->approved,
                    'posted' => false,
                    'created_by' => $item->created_by,
                    'branch' => $item->branch,
                    'batch_number' => $item->batch_number,
                    'reference' => $item->reference
                ]);

                $comm_line = CommissionLines::create([
                    'handler' => $item->created_by,
                    'amount' => 250,
                    'description' => 'Registration Fee for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                    'batch_number' => $item->batch_number,
                    'payment_id' => $payment1->id,
                    'disbursed' => false,
                    'branch' => $item->branch,
                    'transaction_type' => 'registration',
                    'approved' => true,
                ]);

                $item->delete();
            }
        }
        $url = '/sep_customer/' . $acc->customer_id;
        return redirect()->to($url);
    }

    public function pay(Request $request)
    {
        Log::info('$test');
        $savingsAccount = SavingsAccount::where('customer_id', $request->id)->get();
        $total = 0;
        $batch_number = rand(100000000, 999999999);
        $reference = rand(100000000, 999999999);
        $phone = '';
        $customer_id = '';

        foreach ($request->transactions as $item) {
            $acc = SavingsAccount::where('id', $item['id'])->first();
            $customer_id = $acc->customer_id;
            $customer = Customer::where('id', $acc->customer_id)->first();
            $phone =  $customer->phone;

            if ($acc->plan == "Regular") {
                $regfee = Payments::where('customer_id', $acc->customer_id)->where('transaction_type', 'registration')->sum('amount');
                $payment = Payments::create([
                    'savings_account_id' => $acc->id,
                    'plan' => $acc->plan,
                    'customer_id' => $acc->customer_id,
                    'customer_name' => $acc->customer,
                    'transaction_type' => 'savings',
                    'status' => 'pending',
                    'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                    'debit' => $item['amount'],
                    'credit' => 0,
                    'amount' => $item['amount'],
                    'requires_approval' => false,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => $request->user()->name,
                    'branch' => $request->user()->branch,
                    'batch_number' => $batch_number,
                    'reference' => $reference
                ]);

                $sep_commision = 0.0025 * $item['amount'];
                $comm_line = CommissionLines::create([
                    'handler' => $request->user()->name,
                    'amount' => $sep_commision,
                    'description' => '1Commission for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                    'batch_number' => $batch_number,
                    'payment_id' => $payment->id,
                    'disbursed' => false,
                    'branch' => $request->user()->branch,
                    'transaction_type' => 'savings',
                    'approved' => false,
                    // 'transaction_type'=>'commission'
                ]);
                $total = $total + $item['amount'];
                // if ($regfee < 1000) {
                //     $payment = Payments::create([
                //         'savings_account_id' => $acc->id,
                //         'plan' => $acc->plan,
                //         'customer_id' => $acc->customer_id,
                //         'customer_name' => $acc->customer,
                //         'transaction_type' => 'savings',
                //         'status' => 'pending',
                //         'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                //         'debit' => $item['amount'] - 1000,
                //         'credit' => 0,
                //         'amount' => $item['amount'] - 1000,
                //         'requires_approval' => false,
                //         'approved' => false,
                //         'posted' => false,
                //         'created_by' => $request->user()->name,
                //         'branch' => $request->user()->branch,
                //         'batch_number' => $batch_number,
                //         'reference' => $reference
                //     ]);

                //     $payment = Payments::create([
                //         'savings_account_id' => $acc->id,
                //         'plan' => $acc->plan,
                //         'customer_id' => $acc->customer_id,
                //         'customer_name' => $acc->customer,
                //         'transaction_type' => 'registration',
                //         'status' => 'pending',
                //         'remarks' => 'Registration Fee from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                //         'debit' => 1000,
                //         'credit' => 0,
                //         'amount' => 1000,
                //         'requires_approval' => false,
                //         'approved' => false,
                //         'posted' => false,
                //         'created_by' => $request->user()->name,
                //         'branch' => $request->user()->branch,
                //         'batch_number' => $batch_number,
                //         'reference' => $reference
                //     ]);

                //     $sep_commision = 0.0025 * ($item['amount'] - 1000);
                //     $comm_line = CommissionLines::create([
                //         'handler' => $request->user()->name,
                //         'amount' => $sep_commision,
                //         'description' => '2Commission for sales of ₦' . number_format(($item['amount'] - 1000), 2) . ' for ' . $acc->customer,
                //         'batch_number' => $batch_number,
                //         'payment_id' => $payment->id,
                //         'disbursed' => false,
                //         'branch' => $request->user()->branch,
                //         'transaction_type' => 'savings',
                //         'approved' => false,
                //         // 'transaction_type'=>'commission'
                //     ]);
                //     $comm_line = CommissionLines::create([
                //         'handler' => $request->user()->name,
                //         'amount' => 250,
                //         'description' => 'Registration Fee for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                //         'batch_number' => $batch_number,
                //         'payment_id' => $payment->id,
                //         'disbursed' => false,
                //         'branch' => $request->user()->branch,
                //         'transaction_type' => 'registration',
                //         'approved' => false,
                //         // 'transaction_type'=>'commission'
                //     ]);
                //     $total = $total + $item['amount'];
                // } else {
                // }
            } else {
                $payment = Payments::create([
                    'savings_account_id' => $acc->id,
                    'plan' => $acc->plan,
                    'customer_id' => $acc->customer_id,
                    'customer_name' => $acc->customer,
                    'transaction_type' => 'savings',
                    'status' => 'pending',
                    'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                    'debit' => $item['amount'],
                    'credit' => 0,
                    'amount' => $item['amount'],
                    'requires_approval' => false,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => $request->user()->name,
                    'branch' => $request->user()->branch,
                    'batch_number' => $batch_number,
                    'reference' => $reference
                ]);

                $sep_commision = 0.0025 * $item['amount'];
                $comm_line = CommissionLines::create([
                    'handler' => $request->user()->name,
                    'amount' => $sep_commision,
                    'description' => '3Commission for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                    'batch_number' => $batch_number,
                    'payment_id' => $payment->id,
                    'disbursed' => false,
                    'branch' => $request->user()->branch,
                    'transaction_type' => 'savings',
                    'approved' => false,
                    // 'transaction_type'=>'commission'
                ]);
                $total = $total + $item['amount'];
            }
        }

        $cust = Customer::where('id', $customer_id)->first();
        $balance = get_total_balance($customer_id);

        //$msg = "Dear " . $cust->name . ". Your payment of NGN " . number_format($total, 0) . " has been received. Thank you for saving with us.";
        $msg = "Thanks for your patronage we rec'vd " . number_format($total, 0) . " for inquires call 09021417778";
        $res = sendSMS($phone, $msg);

        $location = PaymentLocation::create([
            '' => $reference,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'reference' => $reference
        ]);

        return response([
            'success' => true
        ]);
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
                    $loan = LoansModel::where('customer_id', $customer->id)->first();
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

            return view('sales.customer')->with(['customer' => $customer, 'result' => $result, 'plans' => $plans]);
        }
    }

    public function loan($id)
    {
        $customer = Customer::where('id', $id)->first();

        return view('sales.loan')->with(['customer' => $customer]);
    }

    public function apply_loan(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required',
            'amount' => 'required|numeric',
            'duration' => 'required',
        ]);

        $customer = Customer::where('id', $request->json('id'))->first();

        $balance = get_total_balance($customer->id);

        $amount = $request->json('amount');

        //check if balance is > than 20% of loan
        if ($balance < ($amount * 0.2)) {
            return response()->json([
                'success' => false,
                'message' => 'Customer does not have enough fund to apply for this loan.'
            ]);
        } else {
            $pending_loan = LoansModel::where('customer_id', $customer->id)->where('loan_status', 'pending')->get();
            if (count($pending_loan) > 0) {
                return response([
                    'success' => false,
                    'message' => 'This member has a pending loan.'
                ]);
            }

            $open_loan = LoansModel::where('customer_id', $customer->id)->where('loan_status', 'open')->get();
            if (count($open_loan) > 0) {
                return response([
                    'success' => false,
                    'message' => 'This member has another loan application applied.'
                ]);
            }

            $running_loan = LoansModel::where('customer_id', $customer->id)->where('loan_status', '!=', 'closed')->get();

            if (count($running_loan) > 0) {
                return response([
                    'success' => false,
                    'message' => 'This member has another loan.'
                ]);
            }

            $loan = LoansModel::Create([
                'application_date' => \Carbon\Carbon::now(),
                'customer' => $customer->name,
                'customer_id' => $customer->id,
                'handler' => $customer->handler,
                'branch' => $customer->branch,
                'loan_amount' => $request->amount,
                'percentage' => 5.5,
                'duration' => $request->duration,
                'loan_status' => 'pending',
                'purpose' => $request->purpose,
                'remarks' => 'New Loan',
                'disbursed' => false,
                'disbursement_mode' => '',

                'total_interest' => $request->amount * 5.5 / 100 * $request->duration,
                'total_interest_paid' => 0,
                'monthly_interest' => $request->amount * 5.5 / 100,
                'monthly_principle' => $request->amount / $request->duration,
                'total_monthly_payment' => ($request->amount * 5.5 / 100) + ($request->amount / $request->duration)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan applied successfully'
            ]);
        }
    }

    public function repay_loan($id)
    {
        $customer = Customer::where('id', $id)->first();
        $loan = LoansModel::where('customer_id', $customer->id)->first();
        // if ($loan != null) {
        //     $loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'confirmed')->sum('amount');
        //     $loan_balance = $loan->amount - $loan_repayment - $loan->paid;
        //     $principle = $loan->amount / $loan->duration;
        //     $interest = $loan->amount * ((float)$loan->interest_percentage / 100);
        //     $loan['monthly_paid'] = $loan_repayment;
        //     $loan['balance'] = number_format($loan_balance + $interest, 2);
        //     $loan['monthly_balance'] = number_format($principle + $interest - $loan_repayment, 2);
        //     $loan['repayment'] = number_format($principle + $interest, 2); // plus 
        //     $pending_loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'pending')->sum('amount');
        //     $loan['pending_loan_repayment'] = $pending_loan_repayment;
        // }
        return view('sales.repay')->with(['customer' => $customer, 'loan' => $loan]);
    }

    public function post_loan_repay(Request $request)
    {
        $loan_no = $request->loan_no;
        $amount = $request->amount;

        $loan = LoansModel::where('id', $loan_no)->first();

        if ($loan != null) {
            $repayment = LoanRepaymentModel::create([
                'loan_number' => $loan->id,
                'name' => $loan->customer,
                'amount' => $request->amount,
                'handler' => auth()->user()->name,
                'branch' => auth()->user()->branch,
                'description' => 'Loan repayment of ' . $request->amount . ' for ' . $loan->customer,
                'status' => 'pending',
                'posted' => false,
                'document_number' => rand(1000000, 9999999)
            ]);

            $msg = "Thanks for your patronage we rec'vd " . number_format($request->amount, 0) . " as loan repayment. for inquires call 09021417778";
            $customer = Customer::where('id', $loan->customer_id)->first();
            sendSMS($customer->phone, $msg);
        }

        return response([
            'success' => true
        ]);
    }

    function get_total_balance($id)
    {
        $accounts = SavingsAccount::where('customer_id', $id)->orderBy('id', 'ASC')->where('active', true)->get();

        $data = array();
        $total_balance = 0;
        foreach ($accounts as $acc) {
            $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
            $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'savings')->where('status', 'pending')->sum('amount');
            $pending_withdrawal = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('amount');
            $pending_penalty = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'penalty')->where('status', 'pending')->sum('amount');
            $plan = Plans::where('id', $acc->plans_id)->first();
            $saving_accounts = array();
            $saving_accounts['details'] = $acc;
            $saving_accounts['plan'] = $plan;
            $saving_accounts['confirmed'] = number_format($confirmed_transaction, 2);

            $saving_accounts['pending_withdrawal'] = number_format(($pending_withdrawal + $pending_penalty) * -1, 2);

            $saving_accounts['pending'] = number_format($pending_transaction, 2);
            $data[] = $saving_accounts;
            Log::warning($confirmed_transaction);
            $total_balance = $total_balance + $confirmed_transaction;
        }
        return $total_balance;
    }

    public function loans(Request $request)
    {

        if (auth()->user()->sales_executive == true) {
            // if ($request->status == null || $request->status == 'all') {
            //     $loans = Loan::where('handler', auth()->user()->name)->get();
            // } else {
            //     $loans = Loan::where('handler', auth()->user()->name)->where('status', $request->status)->get();
            // }

            // foreach ($loans as $loan) {
            //     $repayed = $loan->paid + LoanRepayment::where('loan_number', $loan->id)->where('status', 'confirmed')->sum('amount');
            //     $total_exp_repayment = $loan->amount + (($loan->amount * (5.5 / 100)) * $loan->duration);

            //     $balance = $total_exp_repayment - $repayed;
            //     $loan['balance'] = $balance;
            // }

            $loans = LoansModel::where('handler', auth()->user()->name)->where('loan_status', $request->status)->get();

            return view('sales.loans')->with(['loans' => $loans, 'status' => $request->status]);
        } else {
            return abort(401);
        }
    }

    public function statement($id)
    {

        $payment = Payments::where('savings_account_id', $id)->first();
        $customer = Customer::where('id', $payment->customer_id)->first();
        $payments = Payments::where('savings_account_id', $id)->get();

        return view('sales.statement')->with(['payments' => $payments, 'customer' => $customer]);
    }

    public function create_plan(Request $request, $id)
    {
        $plan = Plans::where('id', $request->plan)->first();
        $customer = Customer::where('id', $id)->first();

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
            'created_by' => auth()->user()->name,
            'active' => true,
            'branch' => auth()->user()->branch,
            'handler' => $customer->name,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);

        return redirect()->to('/customer/' . $customer->id);
    }

    public function withdrawal_by_date($date)
    {
        //get withdrawals by date
        $withdrawals = Payments::where('status', 'confirmed')->whereBetween('reservation_from', [$date, $date])->where('transaction_type', 'withdrawal')->get();
        dd($withdrawals);
        return view('sales.withdrawal_report');
    }

    public function reg_fee_collection(Request $request)
    {
        $data = Payments::where('transaction_type', 'registration')->where('created_by', auth()->user()->name)->get();

        return view('sales.reg_fee_collection')->with(['data' => $data]);
    }

    public function payments_by_date(Request $request)
    {
        $data = Payments::where('transaction_type', $request->type)
            ->where('status', 'confirmed')
            ->whereDate('created_at', Carbon::parse($request->date))
            ->where('created_by', auth()->user()->name)
            ->get();

        return view('sales.payments_by_date')->with(['data' => $data]);
    }

    public function customer_balances()
    {
        $data = DB::select("select customers.id, customers.name, sum(payments.amount) as balance from payments inner join customers on payments.customer_id=customers.id where payments.created_by = '" . auth()->user()->name . "' group by customer_id");
        return view('sales.customer_balances')->with(['data' => $data]);
    }
}
