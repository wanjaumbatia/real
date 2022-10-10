<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoansModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->sales_executive == true) {
            $customers = Customer::where('handler', auth()->user()->name)->get();
            return view('sales.customers')->with(['customers' => $customers]);
        } else if ($user->office_admin == true) {
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
        } else if ($user->branch_manager == true) {
            return view('branch.index');
        } else if ($user->loan_officer == true) {

            $loan_totals = DB::select("SELECT (select sum(loan_amount)from loans_models) as total_loan_amount, (select sum(loan_amount) from loans_models where loan_status='ACTIVE') as total_loan_amount_active, (select sum(loan_amount) from loans_models where loan_status='EXPIRED') as total_loan_amount_expire, (select sum(loan_amount) from loans_models where loan_status='BAD') as total_loan_amount_bad FROM loans_models LIMIT 1;");
            $loan_balances = DB::select("SELECT (select sum(total_balance)from loans_models) as total_balance_amount, (select sum(total_balance) from loans_models where loan_status='ACTIVE') as total_loan_balance_amount_active, (select sum(total_balance) from loans_models where loan_status='EXPIRED') as total_loan_balance_amount_expire, (select sum(total_balance) from loans_models where loan_status='BAD') as total_loan_balance_amount_bad FROM loans_models LIMIT 1;"); 
           

            return view('loans.dashboard')->with(['loan_totals' => $loan_totals[0]]);
        } else if ($user->legal == true) {

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
                from loans inner join users on  loans.handler = users.name and status = 'processing' and loans.legal='1';
            ");

            return view('loans.legal_loans')->with(['loans' => $data]);;
        } else {
            return abort(401);
        }
    }
}
