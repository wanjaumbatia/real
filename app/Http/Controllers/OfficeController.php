<?php

namespace App\Http\Controllers;

use App\Mail\Shortage;
use App\Models\CommissionLines;
use App\Models\LoanRepayment;
use App\Models\Payments;
use App\Models\ShortageLine;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OfficeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->office_admin = true) {
            $seps = User::where('sales_executive', true)->where('branch', auth()->user()->branch)->get();
            $result = array();
            foreach ($seps as $sep) {
                $val = array();
                $val['sep'] = $sep->name;
                $savings = Payments::where('status', 'pending')->where('transaction_type', 'savings')->where('created_by', $sep->name)->sum('debit');
                $withdrawal = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $sep->name)->sum('credit');


                $val['savings'] = $savings;
                $val['withdrawals'] = $withdrawal;
                //$val['loans'] = $loan_tot;
                $result[] = $val;
            }

            $total_savings = Payments::where('status', 'pending')->where('transaction_type', 'savings')->sum('debit');
            $total_withdrawals = Payments::where('status', 'pending')->where('transaction_type', 'savings')->sum('debit');
            return view('office.index')->with(['data' => $result, 'total_savings' => $total_savings, 'total_withdrawals' => $total_withdrawals]);
        } else {
            return abort(401);
        }
    }

    public function receive(Request $request)
    {
        $handler = $request->handler;
        $amount = $request->amount;
        $whole = 0;
        $transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->where('status', 'pending')->get();
        $total_transactions = Payments::where('created_by', $handler)->where('transaction_type', 'savings')->where('status', 'pending')->sum('debit');

        if ($total_transactions < $amount) {
            return back()->withErrors(['You can not reconcile more than the required amount of ₦.' . number_format($total_transactions)]);
        } else if($total_transactions > $amount){
            // handle shortages
            $short = $total_transactions - $amount;
            $reference = rand(100000000, 999999999);
            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'remarks' => 'reconciled by ' . auth()->user()->name,
                    'reference' => $reference
                ]);
                //create commission line
                $commission = 0.0025 * $item->debit;
                $comm_line = CommissionLines::create([
                    'handler' => $request->handler,
                    'amount' => $commission,
                    'description' => 'Commission for sales worth ₦' . number_format($item->debit, 2) . ' for payment reference ' . $reference,
                    'batch_number' => $reference,
                    'payment_id' => $item->id,
                    'disbursed' => false,
                    'branch' => auth()->user()->branch,
                    'approved' => true,
                    // 'transaction_type'=>'commission'
                ]);
            }

            $shortage_line = ShortageLine::create([
                'sales_executive'=>$handler,
                'expected_amount'=>$total_transactions,
                'give_amount'=>$amount,
                'short'=>$total_transactions-$amount,
                'reference'=>$reference,
                'cleared'=>false,
                'office_admin'=>auth()->user()->name,
                'description'=>'Shortage of ₦' . number_format(($total_transactions-$amount), 2) .' from '.$handler.' ',
                'reported'=>false,
                'resolved'=>false,
                'branch'=>auth()->user()->branch,
                'remarks'=>""
            ]);

            //send notifications
            $myEmail = ["wanjaumbatia@gmail.com", 'nwaisemoses@gmail.com', 'davidonyango7872@gmail.com'];
            Mail::to($myEmail)->send(new Shortage($handler, $short, $total_transactions, 0, auth()->user()->branch, auth()->user()->name ));
            
            var_dump( Mail:: failures());
            return redirect()->route('office.list');
        } 
        else {
            //clear sales executive
            $reference = rand(100000000, 999999999);
            foreach ($transactions as $item) {
                $tt = Payments::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'remarks' => 'reconciled by ' . auth()->user()->name,
                    'reference' => $reference
                ]);
                //create commission line
                $commission = 0.0025 * $item->debit;
                $comm_line = CommissionLines::create([
                    'handler' => $request->handler,
                    'amount' => $commission,
                    'description' => 'Commission for sales worth ₦' . number_format($item->debit, 2) . ' for payment reference ' . $reference,
                    'batch_number' => $reference,
                    'payment_id' => $item->id,
                    'disbursed' => false,
                    'branch' => $handler,
                    'approved' => true,
                    // 'transaction_type'=>'commission'
                ]);
            }

            return redirect()->route('office.list');
        }
    }

    public function reconcile($id)
    {
        $transactions = Payments::where('status', 'pending')->where('transaction_type', 'savings')->where('created_by', $id)->get();
        $total = 0;
        foreach ($transactions as $item) {
            $total = $total + $item->amount;
        }

        return view('office.reconcile', ['transactions' => $transactions, 'handler' => $id, 'total' => $total]);
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

    public function recon_page($id)
    {
        $transaction = Payments::where('status', 'pending')->where('id', $id)->first();

        return view('office.reconcile_withdrawal', ['transaction' => $transaction, 'created_by' => $id]);
    }


    public function disburse(Request $request)
    {
        Payments::where('id',$request->id)->update([
            'status' => 'confirmed',
        ]);
        
        $tt =Payments::where('id',$request->id)->first();

        $tt = Payments::where('batch_number', $request->batch_number)->update([
            'status' => 'confirmed',
        ]);

        $tt = CommissionLines::where('batch_number', $request->batch_number)->update([
            'approved' => true,
        ]);

        $transaction = Withdrawal::where('status', 'pending')->where('id', $request->id)->first();

        return redirect()->route('office.list',);
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
