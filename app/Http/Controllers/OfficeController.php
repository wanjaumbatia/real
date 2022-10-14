<?php

namespace App\Http\Controllers;

use App\Imports\BalanceImport;
use App\Mail\Shortage;
use App\Models\Balances;
use App\Models\Branch;
use App\Models\CommissionLines;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanRepaymentModel;
use App\Models\NewBalances;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\RealInvest;
use App\Models\ReconciliationRecord;
use App\Models\SavingsAccount;
use App\Models\ShortageLine;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class OfficeController extends Controller
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

    public function import_balances(Request $request)
    {
        Excel::import(new BalanceImport, $request->file);

        return redirect()->route('home')->with('success', 'Balances Imported Successfully');
    }

    public function recon_val($date)
    {
        return response([
            'data' => $date
        ]);
    }

    public function recon_report_by_date()
    {
        $branch = auth()->user()->branch;
        // $recons = ReconciliationRecord::where('branch', $branch)->latest()->get()->groupBy(function ($item) {
        //     return $item->created_at->format('d-M-y');
        // });

        // $result = array();
        // foreach ($recons as $item) {
        //     $sum = 0;
        //     $data = array();
        //     $data['date'] = $item[0]->created_at->format('d-m-Y');;
        //     foreach ($item as $it) {
        //         $sum = $sum + $it->submited;
        //     }
        //     $data['amount'] = $sum;

        //     $result[] = $data;
        // }

        $recons = Payments::where('branch', auth()->user()->branch)->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
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
        return view('office.recon_report_by_date')->with(['data' => $result]);
    }

    public function recon_statement(Request $request)
    {
        $branch = auth()->user()->branch;
        //$data = DB::select("select created_by as handler, sum(debit) as amount from payments where branch = '" . $branch . "' and remarks!='Opening Balance' and status='confirmed' group by created_by;");
        $data = [];
        $recons = Payments::where('branch', $branch)
            ->whereDate('created_at', Carbon::parse($request->date))
            ->where('status', 'confirmed')
            ->where('remarks', '!=', 'Opening Balance')
            ->latest()->get()->groupBy(function ($item) {
                return $item->created_by;
            });

        $result = array();
        foreach ($recons as $item) {
            $deposits = 0;
            $withdrawals = 0;
            $charges = 0;
            $regfees = 0;
            $data = array();
            $data['date'] = $item[0]->created_at->format('d-m-Y');
            $data['sep'] = $item[0]->created_by;

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

        return view('office.recon_statement')->with(['data' => $result]);
    }

    public function recon_per_ref($id)
    {
        $payments = Payments::where('reconciliation_reference', $id)->get();
        $loans = LoanRepayment::where('reconciliation_reference', $id)->get();

        $payments_sum = Payments::where('reconciliation_reference', $id)->sum('amount');
        $loans_sum = LoanRepayment::where('reconciliation_reference', $id)->sum('amount');

        return view('office.recon_per_ref')->with([
            'payments' => $payments,
            'loans' => $loans,
            'payments_sum' => $payments_sum,
            'loans_sum' => $loans_sum
        ]);
    }

    public function backend()
    {
        $branches = Branch::get();

        return view('office.backend')->with(['branches' => $branches]);
    }

    public function seps($name)
    {
        $seps = User::where('branch', $name)->where('sales_executive', true)->get();
        return view('office.seps')->with(['seps' => $seps]);
    }

    public function customer_seps($name)
    {
        $customers = Customer::where('handler', $name)->get();
        return view('office.seps_customers')->with(['customers' => $customers]);
    }

    public function search_customer(Request $request)
    {
        if ($request->userid !== null) {
            $data = DB::select("select * from customers where name like '%" . $request->userid . "%'");
            return view('search')->with(['customer' => $data]);
        }
        return view('search')->with(['customer' => null]);
    }

    public function make_deposit(Request $request)
    {
        $batch_number = rand(100000000, 999999999);
        $reference = rand(100000000, 999999999);

        $acc = SavingsAccount::where('id', $request->id)->first();
        $customer_id = $acc->customer_id;
        $customer = Customer::where('id', $acc->customer_id)->first();
        $payment = Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'pending',
            'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($request->amount, 2),
            'debit' => $request->amount,
            'credit' => 0,
            'amount' => $request->amount,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name,
            'branch' => $request->user()->branch,
            'batch_number' => $batch_number,
            'reference' => $reference,
            'created_at' => $request->date
        ]);

        $cust = Customer::where('id', $customer_id)->first();
        $balance = get_total_balance($customer_id);



        return response([
            'success' => true
        ]);
    }

    public function customer($id)
    {
        $customer = Customer::where('id', $id)->first();
        $balances = NewBalances::where('userid', $customer->username)->get();
        $plans = Plans::get();
        $loan_repayments = LoanRepayment::where('status', 'pending')->where('no', $customer->no)->get();
        $accounts = SavingsAccount::where('customer_id', $customer->id)->get();
        $savings = Payments::where('customer_id', $customer->id)->where('transaction_type', 'savings')->get();
        $withdrawals = Payments::where('customer_id', $customer->id)->where('transaction_type', 'withdrawal')->get();

        foreach ($accounts as $item) {
            $item['balance'] = Payments::where('status', 'confirmed')->where('transaction_type', 'savings')->where('savings_account_id', $item->id)->sum('amount');
            $item['pending'] = Payments::where('status', 'pending')->where('transaction_type', 'savings')->where('savings_account_id', $item->id)->sum('amount');
            $item['reefee'] = Payments::where('transaction_type', 'registration')->where('savings_account_id', $item->id)->sum('amount');
        }

        $seps = User::where('sales_executive', true)->where('branch', $customer->branch)->get();

        return view('office.customer')->with([
            'customer' => $customer,
            'balances' => $balances,
            'plans' => $plans,
            'loan_repayments' => $loan_repayments,
            'accounts' => $accounts,
            'seps' => $seps,
            'savings' => $savings,
            'withdrawals' => $withdrawals
        ]);
    }

    public function delete_saving_account($id)
    {
        $acc = SavingsAccount::where('id', $id)->first();
        $customer = Customer::where('id', $acc->customer_id)->first();
        $acc->delete();
        $url = '/sep_customer/' . $customer->id;
        return redirect()->to($url);
    }

    public function delete_payment($id)
    {
        $payment = Payments::where('id', $id)->first();
        if ($payment != null) {
            if ($payment->transaction_type == 'withdrawal') {
                $charge = Payments::where('created_at', $payment->created_at)->where('batch_number', $payment->batch_number)->where('transaction_type', 'charge')->first();
                if ($charge != null) {
                    $charge->delete();
                }
            }
        }
        $customer = Customer::where('id', $payment->customer_id)->first();
        $payment->delete();
        $url = '/sep_customer/' . $customer->id;
        return redirect()->to($url);
    }

    public function grouped_by_date()
    {
    }

    public function delete_loan_payment($id)
    {
        $loan = LoanRepayment::where('id', $id)->first();
        $customer = Customer::where('no', $loan->no)->first();
        $loan->delete();
        $url = '/sep_customer/' . $customer->id;
        return redirect()->to($url);
    }

    public function migrate_plan(Request $request)
    {

        $customer = Customer::where('id', $request->customer)->first();
        $plan = Plans::where('id', $request->plan)->first();
        $reference = rand(100000000, 999999999);
        $acc = SavingsAccount::create([
            'customer_id' => $customer->id,
            'customer_number' => $customer->no,
            'plans_id' => $plan->id,
            'name' => $plan->name,
            'created_by' => "Admin",
            'active' => true,
            'branch' => $customer->branch,
            'handler' => $customer->handler,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);

        $payment = Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'confirmed',
            'remarks' => 'Opening Balance',
            'debit' => $request->balance,
            'credit' => 0,
            'amount' => $request->balance,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $customer->handler,
            'branch' => $customer->branch,
            'batch_number' => $reference,
            'reference' => $reference
        ]);

        redirect()->route('sep_customer', $customer->id);
    }

    public function change_phone(Request $request)
    {
        $customer = Customer::where('id', $request->id)->first();

        $cust = Customer::where('id', $customer->id)->update([
            'phone' => $request->new_phone,
        ]);
        $url = '/sep_customer/' . $customer->id;
        return redirect()->to($url);
    }

    public function change_name(Request $request)
    {
        $customer = Customer::where('id', $request->id)->first();

        $cust = Customer::where('id', $customer->id)->update([
            'name' => $request->new_name,
        ]);
        $url = '/sep_customer/' . $customer->id;
        return redirect()->to($url);
    }

    public function change_amount(Request $request)
    {
        $payment = Payments::where('id', $request->id)->first();
        $cust = Payments::where('id', $request->id)->update([
            'debit' => $request->amount,
            'amount' => $request->amount,
        ]);
        $url = '/sep_customer/' . $payment->customer_id;
        return redirect()->to($url);
    }

    public function handler_change(Request $request, $id)
    {
        $cust = Customer::where('id', $id)->update([
            'handler' => $request->handler
        ]);
        $url = '/sep_customer/' . $id;
        return redirect()->to($url);
    }

    public function change_loan_amount(Request $request)
    {
        $payment = LoanRepayment::where('id', $request->id)->first();
        $cust = LoanRepayment::where('id', $request->id)->update([
            'amount' => $request->amount,
        ]);
        $loan = Loan::where('id', $payment->loan_number)->first();

        $url = '/sep_customer/' . $loan->customer_id;
        return redirect()->to($url);
    }

    public function change_plan(Request $request)
    {
        $acc = SavingsAccount::where('id', $request->id)->first();
        $plan = Plans::where('id', $request->plan)->first();
        $account = SavingsAccount::where('id', $request->id)->update([
            'plans_id' => $plan->id,
            'name' => $plan->name,
            'active' => true,
            'plan' => $plan->name
        ]);

        $payments = Payments::where('savings_account_id', $request->id)->update([
            'plan' => $plan->name
        ]);

        $url = '/sep_customer/' . $acc->customer_id;
        return redirect()->to($url);
    }

    public function index()
    {
        if (auth()->user()->office_admin == true) {
            $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and reconciled='0' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='" . auth()->user()->branch . "' order by savings desc;");

            $total_expected = 0;
            foreach ($data as $item) {
                $total_expected = $total_expected + $item->savings + $item->loan_collection - $item->pof;
            }
            return view('office.index')->with(['data' => $data, 'total_expected' => $total_expected]);
        } else {
            return abort(401);
        }
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
        $reference = rand(100000000, 999999999);
        $handler = $request->handler;
        $amount = $request->amount;
        $whole = 0;
        $total_loans = LoanRepayment::where('handler', $handler)->where('status', 'pending')->sum('amount');
        $total_loans2 = LoanRepayment::where('handler', $handler)->where('status', 'pending')->sum('amount');
        $loans = LoanRepayment::where('handler', $handler)->where('status', 'pending')->get();
        $loans2 = LoanRepaymentModel::where('handler', $handler)->where('status', 'pending')->get();

        $pof = Payments::where("remarks", "POF")->where('reconciled', false)->where("status", "confirmed")->where('transaction_type', 'withdrawal')->where('created_by', $handler)->sum("credit");
        $transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->where('status', 'pending')->get();

        $total_transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->where('status', 'pending')->sum('debit');
        $total_regfee = Payments::where('created_by', $handler)->where('transaction_type', 'registration')->where('status', 'pending')->sum('debit');

        if (($total_transactions + $total_regfee - $pof + $total_loans) < $amount) {
            return back()->withErrors(['You can not reconcile more than the required amount of ₦.' . number_format(($total_transactions + $total_regfee - $pof))]);
        } else if (($total_transactions + $total_regfee - $pof + $total_loans) > $amount) {
            // handle shortages
            $rec = ReconciliationRecord::create([
                'handler' => $handler,
                'reconciled_by' => auth()->user()->name,
                'expected' => $total_transactions + $total_regfee - $pof + $total_loans,
                'submited' => $amount,
                'shortage' => true,
                'branch' => auth()->user()->branch,
                'reconciliation_reference' => $reference,
            ]);

            $short = ($total_transactions + $total_regfee - $pof) - $amount;
            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
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

            foreach ($loans as $item) {
                $tt = LoanRepayment::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
                ]);
            }

            foreach ($loans2 as $item) {
                $tt = LoanRepaymentModel::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
                ]);
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

            // $myEmail = [
            //     'charlez.o@reliancegroup.com.ng',
            //     'compliance@reliancegroup.com.ng',
            //     'personnel@reliancegroup.com.ng',
            //     'lucky.nwaise@reliancegroup.com.ng',
            //     'it@reliancegroup.com.ng',
            //     'esther.ugbo@reliancegroup.com.ng',
            //     'nwaisemoses@reliancegroup.com.ng',
            //     'christopher.om@reliancegroup.com.ng',
            //     "wanjaumbatia@gmail.com",
            //     'davidonyango7872@gmail.com',
            // ];
            // Mail::to($myEmail)->send(new Shortage($handler, $short, ($total_transactions + $total_regfee - $pof + $total_loans), 0, auth()->user()->branch, auth()->user()->name));

            // var_dump(Mail::failures());
            return redirect()->route('office.list');
        } else {
            //clear sales executive
            $rec = ReconciliationRecord::create([
                'handler' => $handler,
                'reconciled_by' => auth()->user()->name,
                'expected' => $total_transactions + $total_regfee - $pof + $total_loans,
                'submited' => $amount,
                'shortage' => false,
                'branch' => auth()->user()->branch,
                'reconciliation_reference' => $reference,
            ]);

            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
                ]);

                $pof = Payments::where("remarks", "POF")->where("status", "confirmed")->where('transaction_type', 'withdrawal')->where('created_by', $handler)->get();

                foreach ($pof as $item) {
                    Payments::where('id', $item->id)->update([
                        'status' => 'confirmed',
                        'reconciled' => true,
                        'reconciliation_reference' => $reference,
                        'reconciled_by' => auth()->user()->name,
                        'admin_reconciled' => true
                    ]);
                }
                //create commission line
                $comm = CommissionLines::where('batch_number', $item->batch_number)->update([
                    'approved' => true,
                    'approved_by' => auth()->user()->name
                ]);
            }

            foreach ($loans as $item) {
                $tt = LoanRepayment::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
                ]);
            }

            foreach ($loans2 as $item) {
                $tt = LoanRepayment::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'reconciliation_reference' => $reference,
                    'reconciled_by' => auth()->user()->name,
                    'admin_reconciled' => true
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
        return ($bal);
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

    public function reset_balances(Request $request)
    {
        $customers = Customer::where('id' > '131494')->get();
        return $customers;
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

        return redirect()->route('office.list');
    }


    public function balance($name)
    {
        $savings = Payments::where('status', 'pending')->where('transaction_type', 'savings')->where('created_by', $name)->sum('debit');
        $regfee = Payments::where('status', 'pending')->where('transaction_type', 'registration')->where('created_by', $name)->sum('debit');
        $withdrawal = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $name)->sum('credit');
    }


    public function loans(Request $request)
    {
        if (auth()->user()->office_admin == true) {
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


            return view('office.loans')->with(['loans' => $data, 'status' => $request->status, 'branch' => $branch]);
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

        return view('office.loan_card')->with(['loan' => $loan, 'customer' => $customer]);
    }


    public function post_withdrawal(Request $request)
    {
        $account = SavingsAccount::where('id', $request->id)->first();
        $balance = Payments::where('savings_account_id', $account->id)->where('status', 'confirmed')->sum('amount');
        $customer = Customer::where('id', $account->customer_id)->first();
        $plan = Plans::where('id', $account->plans_id)->first();
        $total_credit = $request->amount + $request->commission;
        $otp = auth()->user()->email;
        if ($plan->outward == true) {
            //check when account was created
            $to = \Carbon\Carbon::now();
            $from = $account->created_at;

            $diff_in_months = $to->diffInMonths($from);

            if ($diff_in_months <= $plan->duration) {

                $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');

                $plan = Plans::where('name', $account->plan)->first();

                $expected_commision = $request->amount * $plan->charge;

                $approval = false;

                $interest = $request->amount *  $plan->charge;
                $batch_number = rand(100000000, 999999999);
                //create withdrawal line
                $withdrawal = Payments::create([
                    'savings_account_id' => $account->id,
                    'plan' => $account->plan,
                    'customer_id' => $account->customer_id,
                    'customer_name' => $account->customer,
                    'transaction_type' => 'withdrawal',
                    'status' => 'pending',
                    'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                    'debit' => 0,
                    'credit' => $request->amount,
                    'amount' => $request->amount * -1,
                    'requires_approval' => $approval,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => $customer->handler,
                    'branch' =>  $customer->branch,
                    'batch_number' => $otp
                ]);

                //create interest line
                $charge = Payments::create([
                    'savings_account_id' => $account->id,
                    'plan' => $account->plan,
                    'customer_id' => $account->customer_id,
                    'customer_name' => $account->customer,
                    'transaction_type' => 'interest',
                    'status' => 'pending',
                    'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($interest, 2) . " On " . $account->plan . " account.",
                    'debit' => 0,
                    'credit' => $interest,
                    'amount' => $interest * -1,
                    'requires_approval' => $approval,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => $customer->handler,
                    'branch' =>  $customer->branch,
                    'batch_number' => $otp
                ]);
            } else {
                //check pending withdrawal
                $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');


                $plan = Plans::where('name', $account->plan)->first();

                $expected_commision = $request->amount * $plan->charge;

                $approval = false;
                $interest = $request->amount *  $plan->penalty;
                $batch_number = rand(100000000, 999999999);

                //get total
                $totals = $request->amount + $interest;
                $acceptable = $balance - $interest;
                // if ($balance < $totals) {
                //     return response([
                //         "success" => false,
                //         "message" => "You do not have enough balance in this account ttto withdraw ₦" . number_format($request->amount) . ". You can withdraw up to ₦" . number_format($acceptable, 2) . "."
                //     ]);
                // }
                //create withdrawal line
                $withdrawal = Payments::create([
                    'savings_account_id' => $account->id,
                    'plan' => $account->plan,
                    'customer_id' => $account->customer_id,
                    'customer_name' => $account->customer,
                    'transaction_type' => 'withdrawal',
                    'status' => 'pending',
                    'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                    'debit' => 0,
                    'credit' => $request->amount,
                    'amount' => $request->amount * -1,
                    'requires_approval' => $approval,
                    'approved' => false,
                    'posted' => false,
                    'created_by' => $customer->handler,
                    'branch' =>  $customer->branch,
                    'batch_number' => $otp
                ]);

                //create charge line
                // $interest = Payments::create([
                //     'savings_account_id' => $account->id,
                //     'plan' => $account->plan,
                //     'customer_id' => $account->customer_id,
                //     'customer_name' => $account->customer,
                //     'transaction_type' => 'penalty',
                //     'status' => 'confirmed',
                //     'remarks' => 'Penalty for ' . $account->customer . ' of ₦' . number_format($interest, 2) . " On " . $account->plan . " account.",
                //     'debit' => 0,
                //     'credit' => $interest,
                //     'amount' => $interest * -1,
                //     'requires_approval' => $approval,
                //     'approved' => false,
                //     'posted' => false,
                //     'created_by' => $request->user()->name,
                //     'branch' => $request->user()->branch,
                //     'batch_number' => $otp
                // ]);

                $sep_commision = $request->amount * $plan->penalty * $plan->sep_commission;
            }
        } else {
            // if ($balance < $total_credit) {
            //     return response([
            //         "success" => false,
            //         "message" => "You do not have enough balance in this account to withdraw ₦" . number_format($request->amount) . "."
            //     ]);
            // }
            //check pending withdrawal
            $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');
            $pending_charge = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'charge')->sum('credit');

            if ($balance < ($total_credit + $pending_withdrawal + $pending_charge)) {
                // return response([
                //     "success" => false,
                //     "message" => "Unable to process this withdrawal since customer has a pending withdrawal, this amount exceed the remaining balance."
                // ]);
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
                'status' => 'pending',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->amount,
                'amount' => $request->amount * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $customer->handler,
                'branch' =>  $customer->branch,
                'batch_number' => $otp
            ]);

            //create charge line
            $charge = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'charge',
                'status' => 'pending',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->commission, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->commission,
                'amount' => $request->commission * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $customer->handler,
                'branch' => $customer->branch,
                'batch_number' => $otp
            ]);
        }

        return redirect()->to('/sep_customer/' . $customer->id);

        return response([
            'success' => true,
            'message' => 'Posted successfully'
        ]);
    }

    public function reg_fee_collection(Request $request)
    {
        $data = Payments::where('transaction_type', 'registration')->where('branch', auth()->user()->branch)->get();

        return view('office.reg_fee_collection')->with(['data' => $data]);
    }

    public function payments_by_date(Request $request)
    {
        if ($request->sep != null) {
            $data = Payments::where('transaction_type', $request->type)
                ->where('status', 'confirmed')
                ->where('created_by', $request->sep)
                ->whereDate('created_at', Carbon::parse($request->date))
                ->where('branch', auth()->user()->branch)
                ->get();
        } else {
            $data = Payments::where('transaction_type', $request->type)
                ->where('status', 'confirmed')
                ->whereDate('created_at', Carbon::parse($request->date))
                ->where('branch', auth()->user()->branch)
                ->get();
        }

        return view('office.payments_by_date')->with(['data' => $data]);
    }

    public function fix_withdrawals()
    {
        $payments = Payments::where('created_by', 'David Owuor')->chunk(20, function ($payments) {
            foreach ($payments as $payment) {
                $cust = Customer::where('id', $payment->customer_id)->first();
                $payment->branch = $cust->branch;
                $payment->created_by = $cust->handler;
                $payment->update();
            }
        });

        return response([
            'data' => count($payments)
        ]);
    }

    public function fix_reg_fee()
    {
        //$data = Payments::whereDate('created_at', '>', '2022-10-08')->get();
        $data = Payments::whereDate('created_at', Carbon::parse('2022-10-08'))
            ->where('remarks', '!=', 'Opening Balance')
            ->where('transaction_type', '!=', 'withdrawal')
            ->where('transaction_type', '!=', 'charge')
            ->latest()->get()->groupBy(function ($item) {
                return $item->batch_number;
            });

        // foreach ($data as $item) {
        //     dd($item);
        // }
        return view('regfee_fix')->with(['data' => $data]);
    }

    public function expenses_list(Request $request)
    {
        $expenses = Expense::where('branch', auth()->user()->branch)->get();
        return view('office.expenses')->with(['expenses' => $expenses]);
    }

    public function new_expense(Request $request)
    {
        $codes = ExpenseType::all();
        return view('office.new_expense')->with(['codes'=>$codes]);;
    }

    public function post_expense(Request $request)
    {
     
        if (auth()->user()->office_admin != true) {
            return abort(401);
        }

        $expense = Expense::create([
            'branch' => auth()->user()->branch,
            'description'  => $request->description,
            'status' => 'pending',
            'approved' => false,
            'amount' => $request->amount,
            'remarks' => $request->remarks,
            'type' => $request->type,
            'created_by' => auth()->user()->name,
        ]);

        return redirect()->to('/admin_expenses');
    }

    public function expense_types()
    {
        $types = ExpenseType::all();
        return view('operations.expense_types')->with(['types' => $types]);
    }

    public function new_expense_types()
    {
        return view('operations.new_expense_type');
    }

    public function add_expense_type(Request $request)
    {
        $credit = null;
        if ($request->type == 'Credit') {
            $credit = true;
        } else {
            $credit = false;
        }

        $type = ExpenseType::create([
            "expense_type" => $request->name,
            "credit" => $credit
        ]);

        return redirect()->to('/expense_types');
    }

    public function real_invest_pending(Request $request)
    {
        $data = RealInvest::where('branch', auth()->user()->branch)->where('status', 'New')->get();
        return view('office.pending_real_invest')->with(['data' => $data]);
    }

    public function confirm_real_invest(Request $request, $id)
    {
        $invest = RealInvest::where('id', $id)->where('status', 'New')->first();
        if ($invest->is_customer == true) {
            //create plan  and payment
            $customer = Customer::where('id', $invest->customer_id)->first();
            $plan = Plans::where('name', 'Real Invest')->first();

            $acc = SavingsAccount::create([
                'customer_id' => $customer->id,
                'customer_number' => $customer->no,
                'plans_id' => $plan->id,
                'name' => $plan->name,
                'pledge' => 0,
                'created_by' => auth()->user()->name,
                'active' => true,
                'branch' => $customer->branch,
                'handler' => $customer->handler,
                'customer' => $customer->name,
                'plan' => $plan->name
            ]);
            $batch_number = rand(100000000, 999999999);
            $reference = rand(100000000, 999999999);

            $payment = Payments::create([
                'savings_account_id' => $acc->id,
                'plan' => $acc->plan,
                'customer_id' => $acc->customer_id,
                'customer_name' => $acc->customer,
                'transaction_type' => 'savings',
                'status' => 'pending',
                'remarks' => 'Real Invest payment for ' . $acc->customer . ' of ₦' . number_format($invest->amount, 2),
                'debit' => $invest->amount,
                'credit' => 0,
                'amount' => $invest->amount,
                'requires_approval' => false,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $batch_number,
                'reference' => $reference
            ]);

            $invest->start_date = Carbon::now();
            $invest->exit_date = Carbon::now()->addMonths($invest->duration);
            $invest->created = true;
            $invest->status = 'Active';
            $invest->update();
        } else {
            //create customer
            $cc = Customer::create([
                'name' => $invest->customer_name,
                'address' => $invest->address,
                'phone' => $invest->phone,
                'posted' => false,
                'no' => get_customer_number(),
                'handler' => $invest->handler,
                'branch' => $invest->branch,
                'created_by' => auth()->user()->name,
            ]);

            $customer = Customer::where('name', $invest->customer_name)->first();

            //create plan  and payment
            $plan = Plans::where('name', 'Real Invest')->first();
            $acc = SavingsAccount::create([
                'customer_id' => $customer->id,
                'customer_number' => $customer->no,
                'customer' => $customer->name,
                'plans_id' => $plan->id,
                'name' => $plan->name,
                'pledge' => 0,
                'created_by' => auth()->user()->name,
                'active' => true,
                'branch' => $customer->branch,
                'handler' => $customer->handler,
                'customer' => $customer->name,
                'plan' => $plan->name
            ]);
            $batch_number = rand(100000000, 999999999);
            $reference = rand(100000000, 999999999);

            $payment = Payments::create([
                'savings_account_id' => $acc->id,
                'plan' => $acc->plan,
                'customer_id' => $acc->customer_id,
                'customer_name' => $acc->customer,
                'transaction_type' => 'savings',
                'status' => 'confirmed',
                'remarks' => 'Real Invest payment for ' . $acc->customer . ' of ₦' . number_format($invest->amount, 2),
                'debit' => $invest->amount,
                'credit' => 0,
                'amount' => $invest->amount,
                'requires_approval' => false,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $batch_number,
                'reference' => $reference
            ]);

            $invest->start_date = Carbon::now();
            $invest->exit_date = Carbon::now()->addMonths($invest->duration);
            $invest->created = true;
            $invest->status = 'Active';
            $invest->update();
        }
        return redirect()->to('/active_real_invest');
    }

    public function active_real_invest()
    {
        $data = RealInvest::where('branch', auth()->user()->branch)->where('status', 'Active')->get();
        return view('office.real_invest')->with(['data' => $data]);
    }

    public function withdrawn_real_invest()
    {
        $data = RealInvest::where('branch', auth()->user()->branch)->where('withdrawn', true)->get();
        dd($data);
        // return view('office.pending_real_invest')->with(['data' => $data]);
    }

}
