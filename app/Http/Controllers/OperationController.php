<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CashFlow;
use App\Models\CashSummary;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\LoanDeduction;
use App\Models\LoanForm;
use App\Models\LoanRepayment;
use App\Models\LoanSecurityType;
use App\Models\LoansModel;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    public function __construct()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '12000');
        ini_set('request_terminate_time', '12000');
    }

    public function admin_recon(Request $request)
    {

        if ($request->branch == null) {
            $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and reconciled='0' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='Asaba' order by savings desc;");

            $total_expected = 0;
            foreach ($data as $item) {
                $total_expected = $total_expected + $item->savings + $item->loan_collection - $item->pof;
            }
        } else {
            $data = DB::select("select 
            name,
            IFNULL((select sum(debit) from payments where status = 'pending' and transaction_type='savings' and created_by=u.name),0) as savings,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and created_by=u.name),0) as withdrawals,
            IFNULL((select sum(credit) from payments where status = 'pending' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as unconfirmed_pof,
            IFNULL((select sum(credit) from payments where status = 'confirmed' and reconciled='0' and transaction_type='withdrawal' and remarks='POF' and created_by=u.name),0) as pof,
            IFNULL((select sum(amount) from loan_repayments where status = 'pending' and handler=u.name), 0) as loan_collection
            from users u where sales_executive='1' and branch='" . $request->branch . "' order by savings desc;");

            $total_expected = 0;
            foreach ($data as $item) {
                $total_expected = $total_expected + $item->savings + $item->loan_collection - $item->pof;
            }
        }
        $branches = Branch::all();
        return view('ops.admin_recon')->with(['data' => $data, 'total_expected' => $total_expected, 'branches' => $branches]);
    }

    public function recon_by_date(Request $request)
    {
        $branches = Branch::all();
        $branch = auth()->user()->branch;
        if ($request->branch == null) {
            $recons = Payments::where('branch', 'Asaba')->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
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

            //get loans 
            $loans = LoanRepayment::where('branch', auth()->user()->branch)->where('status', 'confirmed')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });
            return view('ops.reconc_by_date')->with(['data' => $result, 'branches' => $branches]);
        } else {
            $recons = Payments::where('branch', $request->branch)->where('status', 'confirmed')->where('remarks', '!=', 'Opening Balance')->latest()->get()->groupBy(function ($item) {
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

            //get loans 
            $loans = LoanRepayment::where('branch', $request->branch)->where('status', 'confirmed')->latest()->get()->groupBy(function ($item) {
                return $item->created_at->format('d-M-y');
            });

            return view('ops.reconc_by_date')->with(['data' => $result, 'branches' => $branches]);
        }
    }

    public function loan_request(Request $request)
    {
        //check user
        if (auth()->user()->general_manager == true) {
            $loans = LoansModel::where('loan_status', 'processing')
                ->where('loan_officer_approval', true)->get();

            return view('ops.loan_requests')->with(['loans' => $loans]);
        } else if (auth()->user()->managing_director == true) {
            $loans = LoansModel::where('loan_status', 'processing')
                ->where('general_manager_approval', true)
                ->where('loan_amount', '>', 999999)->get();
            return view('ops.loan_requests')->with(['loans' => $loans]);
        }
    }

    public function loan_card(Request $request, $id)
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

        return view('ops.loan_card')
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

    public function cash_summary(Request $request)
    {
        if ($request->branch == null) {
            $cs = CashSummary::where('branch', 'Asaba')->get();
            foreach ($cs as $item) {
                $item->Remmitance = Payments::where('transaction_type', 'savings')->where('branch', auth()->user()->branch)
                    ->where('remarks', '!=', 'Opening Balance')->where('status', 'confirmed')
                    ->whereDate('created_at', $item->report_date)->sum('amount');

                $item->Withdrawals =  Payments::where('transaction_type', 'withdrawal')->where('branch', auth()->user()->branch)
                    ->where('remarks', '!=', 'Opening Balance')->where('status', 'confirmed')
                    ->whereDate('created_at', $item->report_date)->sum('credit');

                $item->CashInflow = CashFlow::where('branch', auth()->user()->branch)
                    ->where('status', 'confirmed')->whereDate('created_at', $item->report_date)
                    ->sum('debit');

                $item->CashOutflow = CashFlow::where('branch', auth()->user()->branch)
                    ->where('status', 'confirmed')->whereDate('created_at', $item->report_date)
                    ->sum('credit');

                $item->Expense = Expense::where('branch', auth()->user()->branch)
                    ->where('status', 'comfirmed')->whereDate('created_at', $item->report_date)->sum('amount');

                $item->LoanIssued = LoansModel::where('branch', auth()->user()->branch)
                    ->whereDate('start_date', $item->report_date)->sum('loan_amount');
            }
            return view('ops.cash_summary')->with(["data1" => $cs]);
        } else {
            $cs = CashSummary::where('branch', 'Asaba')->get();
            foreach ($cs as $item) {
                $item->Remmitance = Payments::where('transaction_type', 'savings')->where('branch', $request->branch)
                    ->where('remarks', '!=', 'Opening Balance')->where('status', 'confirmed')
                    ->whereDate('created_at', $item->report_date)->sum('amount');

                $item->Withdrawals =  Payments::where('transaction_type', 'withdrawal')->where('branch', $request->branch)
                    ->where('remarks', '!=', 'Opening Balance')->where('status', 'confirmed')
                    ->whereDate('created_at', $item->report_date)->sum('credit');

                $item->CashInflow = CashFlow::where('branch', $request->branch)
                    ->where('status', 'confirmed')->whereDate('created_at', $item->report_date)
                    ->sum('debit');

                $item->CashOutflow = CashFlow::where('branch', $request->branch)
                    ->where('status', 'confirmed')->whereDate('created_at', $item->report_date)
                    ->sum('credit');

                $item->Expense = Expense::where('branch', $request->branch)
                    ->where('status', 'comfirmed')->whereDate('created_at', $item->report_date)->sum('amount');

                $item->LoanIssued = LoansModel::where('branch', $request->branch)
                    ->whereDate('start_date', $item->report_date)->sum('loan_amount');
            }
            return view('ops.cash_summary')->with(["data1" => $cs]);
        }
    }

    public function cash_flow(Request $request)
    {
        $cf = CashFlow::all();
        return view('ops.cashflow')->with(['data' => $cf]);
    }

    public function new_casflow(Request $request)
    {
        $branches = Branch::all();

        return view('ops.add_cashflow')->with(['branches' => $branches]);
    }

    public function post_cashflow(Request $request)
    {
        if ($request->direction == '1') {
            $cf = CashFlow::create([
                'branch' => $request->branch,
                'to' => 'HQ',
                'from' => $request->branch,
                'debit' => 0,
                'credit' => $request->amount,
                'amount' => $request->amount * -1,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => auth()->user()->name,
                'created_at' => $request->date
            ]);
        } else if ($request->direction == '2') {
            $cf = CashFlow::create([
                'branch' => $request->branch,
                'from' => 'HQ',
                'to' => $request->branch,
                'credit' => 0,
                'debit' => $request->amount,
                'amount' => $request->amount,
                'description' => $request->description,
                'status' => 'pending',
                'created_by' => auth()->user()->name,
                'created_at' => $request->date
            ]);
        } else {
        }

        return redirect()->to('/cashflows');
    }

    public function loans_status()
    {

        $data = array();
        $data['active'] = LoansModel::where('loan_status', 'ACTIVE')->count();
        $data['expired'] = LoansModel::where('loan_status', 'EXPIRED')->count();
        $data['bad'] = LoansModel::where('loan_status', 'BAD')->count();
        $data['active_amount'] = LoansModel::where('loan_status', 'ACTIVE')->sum('total_balance');
        $data['expired_amount'] = LoansModel::where('loan_status', 'EXPIRED')->sum('total_balance');
        $data['bad_amount'] = LoansModel::where('loan_status', 'BAD')->sum('total_balance');

        return view('ops.loan_status_summary')->with(['data' => $data]);
    }
}
