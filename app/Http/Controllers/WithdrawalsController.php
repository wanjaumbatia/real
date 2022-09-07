<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
class WithdrawalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->operations_manager == true) {
            $transactions = Withdrawal::all();
        } else if (
            auth()->user()->office_admin == true ||
            auth()->user()->branch_manager == true || auth()->user()->assistant_manager == true
        ) {
            $transactions = Withdrawal::where('branch', auth()->user()->branch)->get();
        } else if (auth()->user()->sales_executive == true) {
            $transactions = Withdrawal::where('handler', auth()->user()->name)->get();
        }else{
            return abort(401);
        }
        
        return view('withdrawals.index')->with(['withdrawals'=>$transactions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->sales_executive == false) {
            return abort(401);
        }
        return view('withdrawals.create');
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
