<?php

use App\Models\Customer;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $fields = $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $user = User::where('email', $fields['username'])->first();
    if (!$user || !Hash::check($fields['password'], $user->password)) {
        return response([
            'message' => 'Bad Credredentials'
        ], 401);
    }

    $token = $user->createToken("token");

    return response([
        'token' => $token->plainTextToken,
        'user' => $user
    ], 200);
});

Route::middleware('auth:sanctum')->get("/customers", function (Request $request) {
    // $url = "http://localhost:8090/api/GetMembersByHandler/" . $request->user()->name;
    // $customers = Http::get($url)->json();
    $customers = Customer::where('handler', $request->user()->name)->get();
    return response([$customers]);
});

Route::get("/customer/{id}", function (Request $request, string $id) {
    // $url = "http://localhost:8090/api/customer/" . $id;
    // $customer = Http::get($url)->json();
    $customer = Customer::where('no', $id)->first();
    return response($customer);
});

Route::get("/balance/{id}", function (Request $request, string $id) {
    $url = "http://localhost:8090/api/accno/" . $id;
    $customer = Http::get($url)->json();
    return response($customer);
});

Route::get("/savings/{id}", function (Request $request, string $id) {
    $pending_transaction = Transactions::where('no', $id)->where('status', 'pending')->get();
    $confirmed_transaction = Transactions::where('no', $id)->where('status', 'confirmed')->get();
    $withdrawals = Withdrawal::where('no', $id)->where('status', 'confirmed')->get();

    $pending = 0;
    $confirmed = 0;
    $total_withdrawal = 0;
    foreach ($withdrawals as $item) {
        $total_withdrawal = $total_withdrawal + $item->amount + $item->commision;
    }

    foreach ($pending_transaction as $tt) {
        $pending = $pending + $tt->amount;
    }
    foreach ($confirmed_transaction as $tt) {
        $confirmed = $confirmed + $tt->amount;
    }

    if ($confirmed > 1000) {
        $savings = $confirmed - 1000 - $total_withdrawal;
        $regfee = 1000;
    } else {
        $savings = 0;
        $regfee = $confirmed;
    }

    $resp = array();
    $resp['savings'] = $savings;
    $resp['pending'] = $pending;
    $resp['regfee'] = $regfee;

    return response($resp);
});

Route::get("/contributions/{id}", function (Request $request, string $id) {
    // $url = "http://localhost:8090/api/contributions/" . $id;
    // $transactions = Http::get($url)->json();

    $contributions = Transactions::where('no', $id)->orderBy('created_at', 'DESC')->get();
    return response($contributions);
});

Route::get("/withdrawals/{id}", function (Request $request, string $id) {

    $contributions = Withdrawal::where('no', $id)->orderBy('created_at', 'DESC')->get();
    // $url = "http://localhost:8090/api/withdrawals/" . $id;
    // $transactions = Http::get($url)->json();
    return response($contributions);
});

Route::middleware('auth:sanctum')->post("/contribute", function (Request $request) {
    $amount = $request->amount;
    $no = $request->no;
    $handler = $request->user()->name;
    $branch = $request->user()->branch;
    $url = "http://localhost:8090/api/new_payment";

    $transaction = Http::post($url, [
        'no' => $no,
        'amount' => $amount
    ])->json();

    // $url = "http://localhost:8090/api/customer/" . $no;
    // $customer = Http::get($url)->json();

    $customer = Customer::where('no', $no)->first();
    $transaction = Transactions::create([
        'no' => $customer['no'],
        'name' => $customer['name'],
        'amount' => $amount,
        'handler' => $handler,
        'branch' => $branch,
        'description' => 'Contribution from ' . $customer['name'] . "-" . $customer['no'],
        'document_number' => rand(1000000, 9999999)
    ]);

    return response($transaction);
});


Route::middleware('auth:sanctum')->post("/withdrawal_request", function (Request $request) {
    $withdrawal_amount = $request->amount;
    $no = $request->no;
    $commission = $request->commission;
    $handler = $request->user()->name;
    $branch = $request->user()->branch;

    $pending_transaction = Transactions::where('no', $no)->where('status', 'pending')->get();
    $confirmed_transaction = Transactions::where('no', $no)->where('status', 'confirmed')->get();
    $withdrawals = Withdrawal::where('no', $no)->where('status', 'confirmed')->get();

    $pending = 0;
    $confirmed = 0;
    $total_withdrawal = 0;
    foreach ($withdrawals as $item) {
        $total_withdrawal = $total_withdrawal + $item->amount + $item->commision;
    }

    foreach ($pending_transaction as $tt) {
        $pending = $pending + $tt->amount;
    }
    foreach ($confirmed_transaction as $tt) {
        $confirmed = $confirmed + $tt->amount;
    }

    if ($confirmed > 1000) {
        $savings = $confirmed - 1000 - $total_withdrawal;
        $regfee = 1000;
    } else {
        $savings = 0;
        $regfee = $confirmed;
    }

    $pending_amount = 0;

    $pending_withdrawal = Withdrawal::where('no', $no)->where('status', 'pending')->get();

    foreach ($pending_withdrawal as $tt) {
        $pending_amount = $pending_amount + $tt->amount;
    }

    if ($withdrawal_amount > $savings) {
        return response([
            'success' => false,
            'message' => 'You cannot withdraw more than customers saving.'
        ]);
    }

    if ($withdrawal_amount + $commission > $savings) {
        return response([
            'success' => false,
            'message' => 'You cannot withdraw more than customers saving plus witdrawal commision.'
        ]);
    }

    if ($withdrawal_amount + $commission + $pending_amount > $savings) {
        return response([
            'success' => false,
            'message' => 'There is a pending withdrawal. Adding this will exceed your remaining savings.'
        ]);
    }

    $approval = false;
    //check pending withdrawals
    // $url = "http://localhost:8090/api/customer/" . $no;
    // $customer = Http::get($url)->json();
    $customer = Customer::where('no', $no)->first();
    if ($commission < $withdrawal_amount * 0.03) {
        $approval = true;
    } else {
        $approval = false;
    }

    $trans = Withdrawal::create([
        'no' => $no,
        'name' => $customer['name'],
        'amount' => $withdrawal_amount,
        'handler' => $customer['handler'],
        'description' => 'Withdrawal for ' . $customer['name'] . "- ₦" . number_format($withdrawal_amount, 0),
        'status' => 'pending',
        'branch' =>  $branch,
        'handler' => $handler,
        'document_number' => rand(1000000, 9999999),
        'comission_amount' => $commission,
        'request_approval' => $approval
    ]);
    return response($trans);
});

Route::post("/withdraw", function (Request $request) {
    $amount = $request->amount;
    $no = $request->no;
    $url = "http://localhost:8090/api/new_payment";

    $transaction = Http::post($url, [
        'no' => $no,
        'amount' => $amount
    ])->json();

    return response($transaction);
});
