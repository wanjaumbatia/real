<?php

use App\Models\BankAccounts;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;;

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

    $loan = Loan::where('no', $id)->where('status', 'running')->first();
    $pending_loan = Loan::where('no', $id)->where('status', 'pending')->first();

    $resp = array();
    $resp['savings'] = $savings;
    $resp['pending'] = $pending;
    $resp['regfee'] = $regfee;
    if ($loan != null) {
        $resp['loan'] = $loan->amount;
    }
    if ($pending_loan != null) {
        $resp['pending_loan'] = $pending_loan->amount;
    }

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
    // $url = "http://localhost:8090/api/new_payment";

    // $transaction = Http::post($url, [
    //     'no' => $no,
    //     'amount' => $amount
    // ])->json();

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

Route::middleware('auth:sanctum')->get("/running_loans/{id}", function (Request $request, $id) {
    $pending_loan = Loan::where('no', $id)->where('status', 'running')->first();
    return response($pending_loan);
});

Route::middleware('auth:sanctum')->get("/pending_loans/{id}", function (Request $request, $id) {
    $pending_loan = Loan::where('no', $id)->where('status', 'Open')->where('status', 'runnins')->get();
    return response($pending_loan);
});


Route::middleware('auth:sanctum')->post("/loan_request", function (Request $request) {
    $customer = Customer::where('no', $request->no)->first();
    $pending_transaction = Transactions::where('no', $request->no)->where('status', 'pending')->get();
    $confirmed_transaction = Transactions::where('no', $request->no)->where('status', 'confirmed')->get();
    $withdrawals = Withdrawal::where('no', $request->no)->where('status', 'confirmed')->get();

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

    //get customer savings
    //check for pending loan
    $pending_loan = Loan::where('no', $customer->no)->where('status', 'pending')->get();
    if (count($pending_loan) > 0) {
        return response([
            'success' => false,
            'message' => 'This member has another pending loan.'
        ]);
    }

    $open_loan = Loan::where('no', $customer->no)->where('status', 'open')->get();
    if (count($open_loan) > 0) {
        return response([
            'success' => false,
            'message' => 'This member has another loan application.'
        ]);
    }

    $running_loan = Loan::where('no', $customer->no)->where('status', 'running')->get();
    if (count($running_loan) > 0) {
        return response([
            'success' => false,
            'message' => 'This member has a running loan.'
        ]);
    }

    $loan = Loan::Create([
        'no' => $customer->no,
        'name' => $customer->name,
        'application_date' => now(),
        'amount' => $request->amount,
        'purpose' => $request->purpose,
        'interest_percentage' => 5.5,
        'duration' => $request->duration,
        'current_savings' => $savings,
        'handler' => auth()->user()->name,
        'status' => 'pending',
        'posted' => false
    ]);

    return response($pending_loan);
});

Route::middleware('auth:sanctum')->post("/loan_repayment", function (Request $request) {
    $loan_no = $request->loan_no;
    $amount = $request->amount;

    $loan = Loan::where('id', $loan_no)->first();

    if ($loan) {
        $repayment = LoanRepayment::create([
            'no' => $loan->no,
            'loan_number' => $loan->id,
            'name' => $loan->name,
            'amount' => $request->amount,
            'handler' => auth()->user()->name,
            'branch' => auth()->user()->branch,
            'description' => 'Loan repayment of ' . $request->amount . ' for ' . $loan->name,
            'status' => 'pending',
            'posted' => false,
            'document_number' => rand(1000000, 9999999)
        ]);
    }

    return response([
        'success' => true
    ]);
});


Route::middleware('auth:sanctum')->get('/running_loans', function (Request $request) {
    $loans = Loan::where('status', 'running')->where('handler', $request->user()->name)->get();
    return response($loans);
});

Route::middleware('auth:sanctum')->get('/dashboard', function (Request $request) {
    //withdrawal 
    $pending_savings = Transactions::where('handler', $request->user()->name)->where('status', 'pending')->get();
    $confirmed_savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->get();

    Carbon::setWeekStartsAt(Carbon::MONDAY);
    Carbon::setWeekEndsAt(Carbon::SUNDAY);

    $weekly_pending_savings = Transactions::where('handler', $request->user()->name)->where('status', 'pending')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_confirmed_savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_pending_savings_total = 0;
    foreach ($weekly_pending_savings as $item) {
        $weekly_pending_savings_total = $weekly_pending_savings_total + $item->amount;
    }

    $weekly_pending_savings = Transactions::where('handler', $request->user()->name)->where('status', 'pending')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_confirmed_savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_pending_savings_total = 0;
    foreach ($weekly_pending_savings as $item) {
        $weekly_pending_savings_total = $weekly_pending_savings_total + $item->amount;
    }
    $weekly_confirmed_savings_total = 0;
    foreach ($weekly_confirmed_savings as $item) {
        $weekly_confirmed_savings_total = $weekly_confirmed_savings_total + $item->amount;
    }

    $weekly_pending_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'pending')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_confirmed_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'confirmed')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
    $weekly_pending_withdrawal_total = 0;
    foreach ($weekly_pending_withdrawal as $item) {
        $weekly_pending_withdrawal_total = $weekly_pending_withdrawal_total + $item->amount;
    }
    $weekly_confirmed_withdrawal_total = 0;
    foreach ($weekly_confirmed_withdrawal as $item) {
        $weekly_confirmed_withdrawal_total = $weekly_confirmed_withdrawal_total + $item->amount;
    }

    $montly_pending_savings = Transactions::where('handler', $request->user()->name)->where('status', 'pending')->whereMonth('created_at', Carbon::now()->month)->get();
    $monthly_confirmed_savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->whereMonth('created_at', Carbon::now()->month)->get();
    $montly_pending_savings_total = 0;
    foreach ($montly_pending_savings as $item) {
        $montly_pending_savings_total = $montly_pending_savings_total + $item->amount;
    }
    $montly_confirmed_savings_total = 0;
    foreach ($monthly_confirmed_savings as $item) {
        $montly_confirmed_savings_total = $montly_confirmed_savings_total + $item->amount;
    }

    $today_pending_savings = Transactions::where('handler', $request->user()->name)->where('status', 'pending')->whereDate('created_at', Carbon::today())->get();
    $today_confirmed_savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->whereDate('created_at', Carbon::today())->get();
    $today_pending_savings_total = 0;
    foreach ($today_pending_savings as $item) {
        $today_pending_savings_total = $today_pending_savings_total + $item->amount;
    }
    $today_confirmed_savings_total = 0;
    foreach ($today_confirmed_savings as $item) {
        $today_confirmed_savings_total = $today_confirmed_savings_total + $item->amount;
    }

    $today_pending_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'pending')->whereDate('created_at', Carbon::today())->get();
    $today_confirmed_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'confirmed')->whereDate('created_at', Carbon::today())->get();
    $today_pending_withdrawal_total = 0;
    foreach ($today_pending_withdrawal as $item) {
        $today_pending_withdrawal_total = $today_pending_withdrawal_total + $item->amount;
    }
    $today_confirmed_withdrawal_total = 0;
    foreach ($today_confirmed_withdrawal as $item) {
        $today_confirmed_withdrawal_total = $today_confirmed_withdrawal_total + $item->amount;
    }

    $montly_pending_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'pending')->whereMonth('created_at', Carbon::now()->month)->get();
    $monthly_confirmed_withdrawal = Withdrawal::where('handler', $request->user()->name)->where('status', 'confirmed')->whereMonth('created_at', Carbon::now()->month)->get();
    $montly_pending_withdrawal_total = 0;
    foreach ($montly_pending_withdrawal as $item) {
        $montly_pending_withdrawal_total = $montly_pending_withdrawal_total + $item->amount;
    }
    $montly_confirmed_withdrawal_total = 0;
    foreach ($monthly_confirmed_withdrawal as $item) {
        $montly_confirmed_withdrawal_total = $montly_confirmed_withdrawal_total + $item->amount;
    }

    $savings = Transactions::where('handler', $request->user()->name)->where('status', 'confirmed')->get();
    $withdrawals = Withdrawal::where('handler', $request->user()->name)->where('status', 'confirmed')->get();
    $total_withdrawal_commission = 0;
    $total_savings_commision = 0;
    foreach ($withdrawals as $item) {
        $total_withdrawal_commission = $total_withdrawal_commission + $item->comission_amount;
    }
    foreach ($savings as $item) {
        $total_savings_commision = $total_savings_commision + $item->amount;
    }

    $commission_on_deposits = $total_savings_commision * 0.0025;
    $commission_on_withdrawal = $total_withdrawal_commission * 0.18;

    $total_commision = ($total_savings_commision + $total_withdrawal_commission);
    //closed loans
    //deposits
    //registration
    $result = array();
    $result['commission_savings'] = number_format($commission_on_deposits, 2);
    $result['commission_withdrawals'] = number_format($commission_on_withdrawal, 2);
    $result['total_commisions'] = number_format(($commission_on_deposits + $commission_on_withdrawal), 2);
    $result['monthly_confirmed_savings'] = number_format($montly_confirmed_savings_total, 0);
    $result['monthly_pending_savings'] = number_format($montly_pending_savings_total, 0);
    $result['monthly_confirmed_withdrawal'] = number_format($montly_confirmed_withdrawal_total, 0);
    $result['monthly_pending_withdrawal'] = number_format($montly_pending_withdrawal_total, 0);
    $result['today_confirmed_savings'] = number_format($today_confirmed_savings_total, 0);
    $result['today_pending_saving'] = number_format($today_pending_savings_total, 0);
    $result['today_confirmed_withdrawal'] = number_format($today_confirmed_withdrawal_total, 0);
    $result['today_pending_withdrawal'] = number_format($today_pending_withdrawal_total, 0);
    $result['weekly_confirmed_withdrawal'] = number_format($weekly_confirmed_withdrawal_total, 0);
    $result['weekly_pending_savings'] = number_format($weekly_pending_savings_total, 0);
    $result['weekly_confirmed_savings'] = number_format($weekly_confirmed_savings_total, 0);

    return response($result);
});

Route::middleware('auth:sanctum')->post("/customer", function (Request $request) {

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
            'created_by' => auth()->user()->name,
            'active' => true,
            'branch' => auth()->user()->branch,
            'handler' => auth()->user()->name,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);
    }

    return response([
        'success' => true,
        'data' => $customer
    ]);
});

Route::middleware('auth:sanctum')->get("/accounts/{id}", function ($id) {
    $accounts = SavingsAccount::where('customer_id', $id)->orderBy('id', 'ASC')->where('active', true)->get();

    return response($accounts);
});

Route::middleware('auth:sanctum')->get("/customers/{id}", function ($id) {
    $customer = Customer::where('id', $id)->first();
    return response($customer);
});

Route::middleware('auth:sanctum')->post("/pay", function (Request $request) {
    $savingsAccount = SavingsAccount::where('customer_id', $request->no)->get();

    if ($request->regular > 0) {
        $acc = SavingsAccount::where('plan', 'Regular')->where('customer_id', $request->no)->first();
        Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'pending',
            'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($request->regular, 2),
            'debit' => $request->regular,
            'credit' => 0,
            'amount' => $request->regular,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name
        ]);
    }

    if ($request->gold > 0) {
        $acc = SavingsAccount::where('plan', 'Real Savings Gold')->where('customer_id', $request->no)->first();
        Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'pending',
            'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($request->gold, 2),
            'debit' => $request->gold,
            'credit' => 0,
            'amount' => $request->gold,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name
        ]);
    }

    if ($request->diamond > 0) {
        $acc = SavingsAccount::where('plan', 'Real Savings Diamond')->where('customer_id', $request->no)->first();
        Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'pending',
            'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($request->diamond, 2),
            'debit' => $request->diamond,
            'credit' => 0,
            'amount' => $request->diamond,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name
        ]);
    }

    if ($request->platinum > 0) {
        $acc = SavingsAccount::where('plan', 'Real Savings Platinum')->where('customer_id', $request->no)->first();
        Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'savings',
            'status' => 'pending',
            'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($request->platinum, 2),
            'debit' => $request->platinum,
            'credit' => 0,
            'amount' => $request->platinum,
            'requires_approval' => false,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name
        ]);
    }

    return response([
        'success' => true
    ]);
});

Route::middleware('auth:sanctum')->get("/confirmed/{no}", function ($no) {
    $savings_accounts = SavingsAccount::where('customer_id', $no)->get();
    $bal = array();

    foreach ($savings_accounts as $item) {
        $acc = Payments::where('savings_account_id', $item->id)->where('status', 'confirmed')->sum('amount');
        $bal[$item->plan] = $acc;
    }

    return response($bal);
});

Route::middleware('auth:sanctum')->get("/pending/{no}", function ($no) {
    $savings_accounts = SavingsAccount::where('customer_id', $no)->get();
    $bal = array();

    foreach ($savings_accounts as $item) {
        $acc = Payments::where('savings_account_id', $item->id)->where('status', 'pending')->sum('amount');
        $bal[$item->plan] = $acc;
    }

    return response($bal);
});

Route::middleware('auth:sanctum')->post("/withdrawal_post", function (Request $request) {
    $savings = Payments::where('savings_account_id', $request->id)->where('status', 'confirmed')->sum('amount');
    $expected_commision = $request->amount * 0.03;

    
    if ($savings < $request->amount + $request->commision) {
        return response([
            "success" => false,
            "message" => "You do not have enough balance in this account to withdraw ₦" . number_format($request->amount) . ". Maximum request is " . number_format(($savings - $request->commision), 0)
        ]);
    }

    $approval = false;
    if ($expected_commision > $request->commission) {
        $approval = true;
    } else {
        $approval = true;
    }

    $acc = SavingsAccount::where('id', $request->id)->first();
    $record = Payments::create([
        'savings_account_id' => $acc->id,
        'plan' => $acc->plan,
        'customer_id' => $acc->customer_id,
        'customer_name' => $acc->customer,
        'transaction_type' => 'withdrawal',
        'status' => 'pending',
        'remarks' => 'Withdrawal from ' . $acc->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $acc->plan . " account.",
        'debit' => 0,
        'credit' => $request->amount,
        'amount' => $request->amount * -1,
        'requires_approval' => $approval,
        'approved' => false,
        'posted' => false,
        'created_by' => $request->user()->name
    ]);

    //get commision structure
    $plan = Plans::where('name', $acc->plan)->first();

    if ($plan->outward == false) {
        //charge the customer 
        $record = Payments::create([
            'savings_account_id' => $acc->id,
            'plan' => $acc->plan,
            'customer_id' => $acc->customer_id,
            'customer_name' => $acc->customer,
            'transaction_type' => 'withdrawal fee',
            'status' => 'pending',
            'remarks' => 'Withdrawal Fee from ' . $acc->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $acc->plan . " account.",
            'debit' => 0,
            'credit' => ($request->amount*0.03),
            'amount' => (($request->amount*0.03) * -1),
            'requires_approval' => $approval,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name
        ]);
        
    }else{

    }

    return response($plan);
});


function get_customer_number()
{
    $customers = Customer::all();
    $no = count($customers) + 1;
    return str_pad($no, 6, '0', STR_PAD_LEFT);
}
