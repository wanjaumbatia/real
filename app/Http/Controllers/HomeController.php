<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoansModel;
use App\Models\Payments;
use App\Models\SavingsAccount;
use Carbon\Carbon;
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
            return redirect()->to('/recon_report_by_date');
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

    public function fix_missing_transactions(Request $request)
    {
        $dd = Payments::where('remarks', 'like', 'Collection from CHUKWUMA PASCALINE IFEOMA%')->get();
        dd($dd);
        //     $data = DB::select("select * from payments where created_at > '2022-10-08' and created_at < '2022-10-09' and transaction_type != 'withdrawal'
        //    and  transaction_type != 'charge' and  transaction_type != 'penalty' and remarks != 'Opening Balance' and transaction_type = 'registration';");;
        //     foreach ($data as $item) {
        //         $x = Payments::where('batch_number', $item->batch_number)->where('transaction_type', 'savings')->first();

        //         $x->debit = $x->debit + 1000;
        //         $x->credit = 0.0;
        //         $x->amount = $x->amount + 1000;
        //         $x->remarks = "Collection from CHUKWUMA PASCALINE IFEOMA of ". number_format($x->amount + 1000, 2);
        //         $x->update();

        //         $dl = Payments::where('id', $item->id)->first();
        //         $dl->delete();
        //     }
        dd($dd);
        return view('regfee_fix')->with(['data' => $dd]);
    }
}
