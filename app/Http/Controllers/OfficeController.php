<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                $transactions = Transactions::where('status', 'pending')->where('handler', $sep->name)->get();
                $withdrawals = Withdrawal::where('status', 'pending')->where('handler', $sep->name)->get();
                $amount = 0;
                $withdrawal_total = 0;
                foreach ($transactions as $tt) {
                    $amount = $amount + $tt->amount;
                }
                foreach ($withdrawals as $tt) {
                    $withdrawal_total = $withdrawal_total + $tt->amount;
                }
                $val['amount'] = $amount;
                $val['withdrawal'] = $withdrawal_total;
                $result[] = $val;
                $amount = 0;
            }
            $whole = 0;
            $whole_withdrawal = 0;
            $all_withdrawals = Withdrawal::where('status', 'pending')->where('branch', auth()->user()->branch)->get();
            foreach ($all_withdrawals as $item) {
                $whole_withdrawal = $whole_withdrawal + $item->amount;
            }
            $all = Transactions::where('status', 'pending')->where('branch', auth()->user()->branch)->get();
            foreach ($all as $item) {
                $whole = $whole + $item->amount;
            }

            return view('office.index')->with(['data' => $result, 'total' => $whole, 'total_withdrawal' => $whole_withdrawal]);
        } else {
            return abort(401);
        }
    }

    public function receive(Request $request)
    {
        $handler = $request->handler;
        $amount = $request->amount;
        $whole = 0;
        $transactions = Transactions::where('handler', $handler)->where('status', 'pending')->get();
        foreach ($transactions as $item) {
            $whole = $whole + $item->amount;
        }

        if ($whole > $amount) {
            return back()->withErrors(['You can not reconcile less than the required amount of ₦.' . number_format($whole)]);
        } else if ($whole < $amount) {
            return back()->withErrors(['You can not reconcile more than the required amount of ₦.' . number_format($whole)]);
        } else {
            foreach ($transactions as $item) {
                $trans = Transactions::where('id', $item->id)->update([
                    'status' => 'confirmed',
                    'confirmed_by' => auth()->user()->name
                ]);
            }

            return redirect()->route('office.list');
        }
    }

    public function reconcile($id)
    {
        $transactions = Transactions::where('status', 'pending')->where('handler', $id)->get();
        $total = 0;
        foreach ($transactions as $item) {
            $total = $total + $item->amount;
        }

        return view('office.reconcile', ['transactions' => $transactions, 'handler' => $id, 'total' => $total]);
    }

    public function withdrawal_list($id)
    {
        if (auth()->user()->office_admin == false) {
            return abort(401);
        }

        $transactions = Withdrawal::where('status', 'pending')->where('handler', $id)->get();
        $total = 0;
        foreach ($transactions as $item) {
            $total = $total + $item->amount;
        }

        return view('office.withdrawal_list', ['transactions' => $transactions, 'handler' => $id, 'total' => $total]);
    }

    public function recon_page($id)
    {
        $transaction = Withdrawal::where('status', 'pending')->where('id', $id)->first();
        
        return view('office.reconcile_withdrawal', ['transaction' => $transaction, 'handler' => $id]);
    }


    public function disburse(Request $request)
    {
        $tt = Withdrawal::where('id', $request->id)->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->user()->name
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
