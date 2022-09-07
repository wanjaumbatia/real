<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->sales_executive == true) {
            $url = "http://localhost:8090/api/GetMembersByHandler/" . auth()->user()->name;
            $customers = Http::get($url)->json();
        } else {
            return abort(401);
        }

        $customeers = Customer::where('posted', 0)->where('handler', auth()->user()->name)->get();
        if ($customeers != null) {
            //$customers[] = $customeers;
        }


        return view('customers.index')->with(['customers' => $customers]);
    }


    public function contribution($id)
    {
        $url = "http://localhost:8090/api/customer/" . $id;
        $customer = Http::get($url)->json();

        $bal_url = "http://localhost:8090/api/accNo/" . $id;
        $balances =  Http::get($bal_url)->json();

        return view('customers.contribution')->with(['customer' => $customer, "balances" => $balances]);
    }

    public function contribute(Request $request)
    {
        $url = "http://localhost:8090/api/customer/" . $request->no;
        $customer = Http::get($url)->json();
        Transactions::create([
            'no'=>$customer['no'],
            'name'=>$customer['name'],
            'amount'=>$request->amount,
            'handler'=>$customer['handler'],
            'description'=>$customer['no'],
            'document_number'=>rand(1000000,9999999)
        ]);

        return 'success';
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (auth()->user()->sales_executive == true || auth()->user()->office_admin == true) {
            return view('customers.create');
        } else {
            abort(401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|numeric|min:10',
            'address' => 'required'
        ]);
        $handler = auth()->user()->name;
        $customer = Customer::create([
            'no' => rand(100000, 999999),
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'town' => $request->town,
            'dob' => $request->dob,
            'address' => $request->address,
            'business' => $request->business,
            'bank' => $request->bank,
            'bankacc' => $request->bankacc,
            'handler' => $handler,
            'branch' => auth()->user()->branch,
        ]);

        return redirect()->route('customers.list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $url = "http://localhost:8090/api/customer/" . $id;
        $customer = Http::get($url)->json();

        $bal_url = "http://localhost:8090/api/accNo/" . $id;
        $balances =  Http::get($bal_url)->json();

        return view('customers.show')->with(['customer' => $customer, "balances" => $balances]);
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
