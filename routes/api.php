<?php

use App\Mail\Shortage;
use App\Models\BankAccounts;
use App\Models\CommissionLines;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\OtpCode;
use App\Models\PaymentLocation;
use App\Models\Payments;
use App\Models\Plans;
use App\Models\SavingsAccount;
use App\Models\ShortageLine;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Support\Facades\Log;;


Route::middleware('auth:sanctum')->get('/mobile/customer/{id}', function ($id) {
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/tokens/create', function (Request $request) {
    $fields = $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $user = User::where('email', $fields['username'])->first();
    if (!$user) {
        return response([
            'message' => 'Bad Credredentials'
        ], 401);
    }

    $token = $user->createToken("token");

    //check if user has shortage
    $shortage = ShortageLine::where('sales_executive', $user->name)
        ->where('cleared', false)->get();

    if (count($shortage) > 0) {
        return response([
            'success' => false,
            'message' => "Please clear your shortage to proceed"
        ]);
    }

    return response([
        'token' => $token->plainTextToken,
        'user' => $user
    ], 200);
});

Route::middleware('auth:sanctum')->get("/customers", function (Request $request) {
    // $url = "http://localhost:8090/api/GetMembersByHandler/" . $request->user()->name;
    // $customers = Http::get($url)->json();
    $customers = Customer::where('handler', $request->user()->name)->get();
    return response($customers);
});

Route::get("/customer/{id}", function (Request $request, string $id) {
    // $url = "http://localhost:8090/api/customer/" . $id;
    // $customer = Http::get($url)->json();
    $customer = Customer::where('no', $id)->first();
    return response($customer);
});


Route::get("/savings/{id}", function (Request $request, string $id) {
    $pending_transaction = Transactions::where('no', $id)->where('status', 'pending')->get();
    $confirmed_transaction = Transactions::where('no', $id)->where('status', 'confirmed')->get();
    $withdrawals = Withdrawal::where('no', $id)->where('status', 'confirmed')->get();



    $confirmed = 0;
    $pending = 0;
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
    $data = array();

    $loan = Loan::where('id', $id)->where('status', 'running')->first();
    $data['loan'] = $loan;
    $loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'confirmed')->sum('amount');
    $loan_balance = $loan->amount - $loan_repayment - $loan->paid;
    $data['balance'] = $loan_balance;
    //calculate interest
    $thirty_days_ago = Carbon::now()->subDays(30);
    $principle = $loan->amount / $loan->duration;
    $interest = $loan->amount * ((float)$loan->interest_percentage / 100);
    $total_repayment = $principle + $interest;
    $data['expected_repayment'] = number_format($total_repayment, 2);

    return response($data);
});

Route::middleware('auth:sanctum')->get("/pending_loans/{id}", function (Request $request, $id) {
    $pending_loan = Loan::where('no', $id)->where('status', 'Open')->where('status', 'runnins')->get();
    return response($pending_loan);
});


Route::middleware('auth:sanctum')->post("/loan_request", function (Request $request) {
    $customer = Customer::where('id', $request->no)->first();

    $balance = get_total_balance($customer->id);


    $limit = $request->amount * 0.2;
    if ($balance < $limit) {
        return response([
            'success' => false,
            'message' => 'Loan cannot be processed, minimum saving threshold not met.'
        ]);
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
        'customer_id' => $customer->id,
        'purpose' => $request->purpose,
        'interest_percentage' => 5.5,
        'duration' => $request->duration,
        'current_savings' => $balance,
        'handler' => auth()->user()->name,
        'status' => 'pending',
        'posted' => false
    ]);

    return response($loan);
});

Route::middleware('auth:sanctum')->post("/loan_repayment", function (Request $request) {
    $loan_no = $request->loan_no;
    $amount = $request->amount;

    Log::warning($request);

    $loan = Loan::where('id', $loan_no)->first();

    if ($loan != null) {
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

        $msg = "Thanks for your patronage we rec'vd " . number_format($request->amount, 0) . " as loan repayment. for inquires call 09021417778";
        $customer = Customer::where('customer_id', $loan->customer_id)->first();
        //sendSMS($customer->phone, $msg);
    }

    return response([
        'success' => true
    ]);
});


Route::middleware('auth:sanctum')->get('/running_loans', function (Request $request) {
    $loans = Loan::where('status', 'running')->where('handler', $request->user()->name)->get();
    return response($loans);
});

Route::middleware('auth:sanctum')->get('/dashboard12', function (Request $request) {
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
        'email' => $request->email,
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
            'name' => 'Regular',
            'pledge' => 0,
            'created_by' => auth()->user()->name,
            'active' => true,
            'branch' => auth()->user()->branch,
            'handler' => auth()->user()->name,
            'customer' => $customer->name,
            'plan' => $plan->name
        ]);
    }

    //send sms
    $phone = $customer->phone;
    $msg = "Dear " . $customer->name . ". Your registration to Reliance Economic Advancement LTD has been approved. Your unique customer number is " . $customer->no . ".";
    $res = sendSMS($phone, $msg);

    return response([
        'success' => true,
        'data' => $customer
    ]);
});

Route::middleware('auth:sanctum')->get("/accounts/{id}", function ($id) {
    $accounts = SavingsAccount::where('customer_id', $id)->orderBy('id', 'ASC')->where('active', true)->get();

    $data = array();
    foreach ($accounts as $acc) {
        $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
        $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'savings')->where('status', 'pending')->sum('amount');
        $pending_withdrawal = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('amount');
        $pending_penalty = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'penalty')->where('status', 'pending')->sum('amount');

        $plan = Plans::where('id', $acc->plans_id)->first();
        $saving_accounts = array();
        $saving_accounts['details'] = $acc;
        $loan = Loan::where('customer_id', $acc->customer_id)->first();

        if ($loan != null) {
            $loan_repayment = LoanRepayment::where('loan_number', $loan->id)->sum('amount');
            $loan_balance = $loan->amount - $loan_repayment;
            $loan['balance'] = $loan_balance;
            $saving_accounts['loan'] = $loan;
        }

        $saving_accounts['plan'] = $plan;
        $saving_accounts['confirmed'] = $confirmed_transaction;
        $saving_accounts['pending'] = $pending_transaction;
        $saving_accounts['pending_withdrawal'] = ($pending_withdrawal + $pending_penalty) * -1;

        $data[] = $saving_accounts;
    }


    return response($data);
});

Route::middleware('auth:sanctum')->get("/loans/{id}", function ($id) {
    $loan = Loan::where('customer_id', $id)->where('status', 'pending')->get();
    return response($loan);
});

Route::middleware('auth:sanctum')->get("/account/{id}", function ($id) {
    $acc = SavingsAccount::where('id', $id)->first();

    $data = array();
    $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
    $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'pending')->where('transaction_type', 'savings')->sum('amount');
    $pending_withdrawals = Payments::where('savings_account_id', $acc->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('amount');
    $plan = Plans::where('id', $acc->plans_id)->first();

    $to = \Carbon\Carbon::now();
    $from = $acc->created_at;
    $diff_in_months = $to->diffInMonths($from);

    $saving_accounts = array();
    if ($diff_in_months > $plan->duration) {
        $saving_accounts['mature'] = true;
    } else {
        $saving_accounts['mature'] = false;
    }
    $saving_accounts['details'] = $acc;
    $saving_accounts['plan'] = $plan;
    $saving_accounts['confirmed'] = $confirmed_transaction;
    $saving_accounts['pending'] = $pending_transaction;
    $saving_accounts['pending_withdrawal'] = $pending_withdrawals;
    return response($saving_accounts);
});

Route::middleware('auth:sanctum')->post("/verify_number/{id}", function ($id, Request $request) {
    $customer = Customer::where('id', $id)->first();

    if ($customer->phone == $request->phone) {
        $customer = Customer::where('id', $id)->update([
            'phone_verified' => true,
        ]);
        return response([
            'success' => true,
            'message' => 'Verified Successfully'
        ]);
    } else {
        //send otp
        $otp = rand(000000, 999999);
        $msg = 'Hello ' . $customer->name . ' Welcome to REAL cooperative APP REALdoe. Use this OTP ' . $otp . ' to validate your account. For enquires call 09021417778.';
        // $res = OtpCode::create([
        //     'code' => $otp,
        //     'user_id' => $request->user()->id
        // ]);
        Log::warning($msg);
        sendSMS($request->phone, $msg);
        return response([
            'success' => false,
            'message' => 'need verification',
            'otp' => $otp
        ]);
    }
});

Route::middleware('auth:sanctum')->post("/update_customer", function (Request $request) {
    //check if phone number is in syste,
    //check dublicate number
    // $phone_check = Customer::where('phone', $request->phone)->get();

    // if (count($phone_check) > 0) {
    //     return response([
    //         'success' => false,
    //         'message' => 'Phone number is already in use by another customer'
    //     ]);
    // }
    $customer = Customer::where('id', $request->id)->update([
        'phone_verified' => true,
        'phone' => $request->phone
    ]);

    return response([
        "success" => true,
        "message" => "Verified Successfully"
    ]);
});

function get_total_balance($id)
{
    $accounts = SavingsAccount::where('customer_id', $id)->orderBy('id', 'ASC')->where('active', true)->get();

    $data = array();
    $total_balance = 0;
    foreach ($accounts as $acc) {
        $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
        $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'savings')->where('status', 'pending')->sum('amount');
        $pending_withdrawal = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('amount');
        $pending_penalty = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'penalty')->where('status', 'pending')->sum('amount');
        $plan = Plans::where('id', $acc->plans_id)->first();
        $saving_accounts = array();
        $saving_accounts['details'] = $acc;
        $saving_accounts['plan'] = $plan;
        $saving_accounts['confirmed'] = number_format($confirmed_transaction, 2);

        $saving_accounts['pending_withdrawal'] = number_format(($pending_withdrawal + $pending_penalty) * -1, 2);

        $saving_accounts['pending'] = number_format($pending_transaction, 2);
        $data[] = $saving_accounts;
        Log::warning($confirmed_transaction);
        $total_balance = $total_balance + $confirmed_transaction;
    }
    return $total_balance;
}


Route::middleware('auth:sanctum')->get("/customers/{id}", function ($id) {
    $customer = Customer::where('id', $id)->first();
    $plans = Plans::where('active', true)->get();
    //get accounts
    $accounts = SavingsAccount::where('customer_id', $customer->id)->orderBy('id', 'ASC')->where('active', true)->get();

    $data = array();
    $total_balance = 0;
    foreach ($accounts as $acc) {
        $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->sum('amount') - Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'registration')->where('status', 'confirmed')->sum('amount');
        $pending_transaction = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'savings')->where('status', 'pending')->sum('amount');
        $pending_withdrawal = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'withdrawal')->where('status', 'pending')->sum('amount');
        $pending_penalty = Payments::where('savings_account_id', $acc->id)->where('transaction_type', 'penalty')->where('status', 'pending')->sum('amount');
        $plan = Plans::where('id', $acc->plans_id)->first();
        $saving_accounts = array();
        $saving_accounts['details'] = $acc;
        $saving_accounts['plan'] = $plan;
        $saving_accounts['confirmed'] = number_format($confirmed_transaction, 2);
        $saving_accounts['pending_withdrawal'] = number_format(($pending_withdrawal + $pending_penalty) * -1, 2);
        if ($acc->plan == "Regular") {
            $loan = Loan::where('customer_id', $customer->id)->first();
            if ($loan != null) {
                $pend_loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'pending')->sum('amount');
                $pending_transaction = $pending_transaction + $pend_loan_repayment;
            }
        }
        $saving_accounts['pending'] = number_format($pending_transaction, 2);
        $data[] = $saving_accounts;
        $total_balance = $total_balance + $confirmed_transaction;
    }

    $loan = Loan::where('customer_id', $customer->id)->first();
    if ($loan != null) {
        $loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'confirmed')->sum('amount');
        $loan_balance = $loan->amount - $loan_repayment - $loan->paid;
        $principle = $loan->amount / $loan->duration;
        $interest = $loan->amount * ((float)$loan->interest_percentage / 100);
        $loan['monthly_paid'] = $loan_repayment;
        $loan['balance'] = number_format($loan_balance + $interest, 2);
        $loan['monthly_balance'] = number_format($principle + $interest - $loan_repayment, 2);
        $loan['repayment'] = number_format($principle + $interest, 2); // plus 
        $pending_loan_repayment = LoanRepayment::where('loan_number', $loan->id)->where('status', 'pending')->sum('amount');
        $loan['pending_loan_repayment'] = $pending_loan_repayment;
    }

    $result = array();
    $result['customer'] = $customer;
    $result['accounts'] = $data;
    $result['plans'] = $plans;
    $result['loan'] = $loan;
    $result['total_balance'] = $total_balance;

    return response($result);
});

Route::middleware('auth:sanctum')->post("/pay", function (Request $request) {
    $savingsAccount = SavingsAccount::where('customer_id', $request->id)->get();
    $total = 0;
    $batch_number = rand(100000000, 999999999);
    $reference = rand(100000000, 999999999);
    $phone = '';
    $customer_id = '';

    foreach ($request->transactions as $item) {
        $acc = SavingsAccount::where('id', $item['id'])->first();
        $customer_id = $acc->customer_id;
        $customer = Customer::where('id', $acc->customer_id)->first();
        $phone =  $customer->phone;

        if ($acc->plan == "Regular") {
            $regfee = Payments::where('customer_id', $acc->customer_id)->where('transaction_type', 'registration')->sum('amount');
            $payment = Payments::create([
                'savings_account_id' => $acc->id,
                'plan' => $acc->plan,
                'customer_id' => $acc->customer_id,
                'customer_name' => $acc->customer,
                'transaction_type' => 'savings',
                'status' => 'pending',
                'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                'debit' => $item['amount'],
                'credit' => 0,
                'amount' => $item['amount'],
                'requires_approval' => false,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $batch_number,
                'reference' => $reference
            ]);

            $sep_commision = 0.0025 * $item['amount'];
            $comm_line = CommissionLines::create([
                'handler' => $request->user()->name,
                'amount' => $sep_commision,
                'description' => '1Commission for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                'batch_number' => $batch_number,
                'payment_id' => $payment->id,
                'disbursed' => false,
                'branch' => $request->user()->branch,
                'transaction_type' => 'savings',
                'approved' => false,
                // 'transaction_type'=>'commission'
            ]);
            $total = $total + $item['amount'];
            // if ($regfee < 1000) {

            //     $payment = Payments::create([
            //         'savings_account_id' => $acc->id,
            //         'plan' => $acc->plan,
            //         'customer_id' => $acc->customer_id,
            //         'customer_name' => $acc->customer,
            //         'transaction_type' => 'savings',
            //         'status' => 'pending',
            //         'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
            //         'debit' => $item['amount'] - 1000,
            //         'credit' => 0,
            //         'amount' => $item['amount'] - 1000,
            //         'requires_approval' => false,
            //         'approved' => false,
            //         'posted' => false,
            //         'created_by' => $request->user()->name,
            //         'branch' => $request->user()->branch,
            //         'batch_number' => $batch_number,
            //         'reference' => $reference
            //     ]);

            //     $payment = Payments::create([
            //         'savings_account_id' => $acc->id,
            //         'plan' => $acc->plan,
            //         'customer_id' => $acc->customer_id,
            //         'customer_name' => $acc->customer,
            //         'transaction_type' => 'registration',
            //         'status' => 'pending',
            //         'remarks' => 'Registration Fee from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
            //         'debit' => 1000,
            //         'credit' => 0,
            //         'amount' => 1000,
            //         'requires_approval' => false,
            //         'approved' => false,
            //         'posted' => false,
            //         'created_by' => $request->user()->name,
            //         'branch' => $request->user()->branch,
            //         'batch_number' => $batch_number,
            //         'reference' => $reference
            //     ]);

            //     $sep_commision = 0.0025 * ($item['amount'] - 1000);
            //     $comm_line = CommissionLines::create([
            //         'handler' => $request->user()->name,
            //         'amount' => $sep_commision,
            //         'description' => '2Commission for sales of ₦' . number_format(($item['amount'] - 1000), 2) . ' for ' . $acc->customer,
            //         'batch_number' => $batch_number,
            //         'payment_id' => $payment->id,
            //         'disbursed' => false,
            //         'branch' => $request->user()->branch,
            //         'transaction_type' => 'savings',
            //         'approved' => false,
            //         // 'transaction_type'=>'commission'
            //     ]);
            //     $comm_line = CommissionLines::create([
            //         'handler' => $request->user()->name,
            //         'amount' => 250,
            //         'description' => 'Registration Fee for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
            //         'batch_number' => $batch_number,
            //         'payment_id' => $payment->id,
            //         'disbursed' => false,
            //         'branch' => $request->user()->branch,
            //         'transaction_type' => 'registration',
            //         'approved' => false,
            //         // 'transaction_type'=>'commission'
            //     ]);
            //     $total = $total + $item['amount'];
            // } else {

            // }

        } else {
            $payment = Payments::create([
                'savings_account_id' => $acc->id,
                'plan' => $acc->plan,
                'customer_id' => $acc->customer_id,
                'customer_name' => $acc->customer,
                'transaction_type' => 'savings',
                'status' => 'pending',
                'remarks' => 'Collection from ' . $acc->customer . ' of ₦' . number_format($item['amount'], 2),
                'debit' => $item['amount'],
                'credit' => 0,
                'amount' => $item['amount'],
                'requires_approval' => false,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $batch_number,
                'reference' => $reference
            ]);

            $sep_commision = 0.0025 * $item['amount'];
            $comm_line = CommissionLines::create([
                'handler' => $request->user()->name,
                'amount' => $sep_commision,
                'description' => '3Commission for sales of ₦' . number_format($item['amount'], 2) . ' for ' . $acc->customer,
                'batch_number' => $batch_number,
                'payment_id' => $payment->id,
                'disbursed' => false,
                'branch' => $request->user()->branch,
                'transaction_type' => 'savings',
                'approved' => false,
                // 'transaction_type'=>'commission'
            ]);
            $total = $total + $item['amount'];
        }
    }

    $cust = Customer::where('id', $customer_id)->first();
    $balance = get_total_balance($customer_id);

    //$msg = "Dear " . $cust->name . ". Your payment of NGN " . number_format($total, 0) . " has been received. Thank you for saving with us.";
    $msg = "Thanks for your patronage we rec'vd " . number_format($total, 0) . " your bal is " . number_format($balance, 0) . " for inquires call 09021417778";
    //$res = sendSMS($phone, $msg);

    $location = PaymentLocation::create([
        '' => $reference,
        'longitude' => $request->longitude,
        'latitude' => $request->latitude,
        'reference' => $reference
    ]);

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
        $acc = Payments::where('savings_account_id', $item->id)->where('status', 'pending')->where('transaction_type', 'savings')->sum('amount');
        $bal[$item->plan] = $acc;
    }

    return response($bal);
});

Route::get('/plans', function (Request $request) {
    $plans = Plans::where('active', true)->get();
    return response($plans);
});

Route::get("/create_account", function (Request $request) {
    $customers = Customer::all();
    foreach ($customers as $customer) {
        //create default account
        $plans = Plans::where('default', true)->where('active', true)->get();

        foreach ($plans as $plan) {
            //create savings account
            $account = SavingsAccount::create([
                'customer_id' => $customer->id,
                'customer_number' => $customer->no,
                'plans_id' => $plan->id,
                'name' => 'Regular',
                'created_by' => "Admin",
                'active' => true,
                'branch' => $customer->branch,
                'handler' => $customer->handler,
                'customer' => $customer->name,
                'plan' => $plan->name
            ]);
        }
    }
    return response("Success");
});

Route::middleware('auth:sanctum')->post("/verify_withdrawal", function (Request $request) {

    $payments = Payments::where('batch_number', $request->otp)->get();
    $reference = rand(100000000, 999999999);
    foreach ($payments as $item) {
        if ($request->payment == "Pay On Field") {
            //handle request approval for disbursement
            $tt = Payments::where('id', $item->id)->update([
                'status' => 'pending',
                'batch_number' => $reference,
                'remarks' => "POF"
            ]);
        } else if ($request->payment == "Office Admin") {
            $tt = Payments::where('id', $item->id)->update([
                'status' => 'pending',
                'batch_number' => $reference
            ]);
        } else if ($request->payment == "Bank Transfer") {
            $tt = Payments::where('id', $item->id)->update([
                'status' => 'pending',
                'batch_number' => $reference,
                'cps' => true
            ]);
        }
    }

    return response($payments);
});


Route::middleware('auth:sanctum')->post("/withdrawal_post", function (Request $request) {
    //calculate
    $account = SavingsAccount::where('id', $request->id)->first();
    $balance = Payments::where('savings_account_id', $account->id)->where('status', 'confirmed')->sum('amount');

    $customer = Customer::where('id', $account->customer_id)->first();

    $plan = Plans::where('id', $account->plans_id)->first();

    $total_credit = $request->amount + $request->commission;
    $otp = rand(000000, 999999);


    if ($plan->outward == true) {
        //check when account was created
        $to = \Carbon\Carbon::now();
        $from = $account->created_at;

        $diff_in_months = $to->diffInMonths($from);

        if ($diff_in_months >= $plan->duration) {

            $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');

            if ($balance < $total_credit + $pending_withdrawal) {
                return response([
                    "success" => false,
                    "message" => "Unable to process this withdrawal since customer has a pending withdrawal, this amount exceed the remaining balance."
                ]);
            }



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
                'status' => 'open',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->amount,
                'amount' => $request->amount * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $otp
            ]);

            //create interest line
            $charge = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'interest',
                'status' => 'open',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($interest, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $interest,
                'amount' => $interest * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $otp
            ]);
        } else {

            //check pending withdrawal
            $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');

            if ($balance < $total_credit + $pending_withdrawal) {
                return response([
                    "success" => false,
                    "message" => "Unable to process this withdrawal since customer has a pending withdrawal, this amount exceed the remaining balance."
                ]);
            }

            $plan = Plans::where('name', $account->plan)->first();

            $expected_commision = $request->amount * $plan->charge;

            $approval = false;
            $interest = $request->amount *  $plan->penalty;
            $batch_number = rand(100000000, 999999999);

            //get total
            $totals = $request->amount + $interest;
            $acceptable = $balance - $interest;
            if ($balance < $totals) {
                return response([
                    "success" => false,
                    "message" => "You do not have enough balance in this account ttto withdraw ₦" . number_format($request->amount) . ". You can withdraw up to ₦" . number_format($acceptable, 2) . "."
                ]);
            }
            //create withdrawal line
            $withdrawal = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'withdrawal',
                'status' => 'open',
                'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $request->amount,
                'amount' => $request->amount * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $otp
            ]);

            //create charge line
            $interest = Payments::create([
                'savings_account_id' => $account->id,
                'plan' => $account->plan,
                'customer_id' => $account->customer_id,
                'customer_name' => $account->customer,
                'transaction_type' => 'penalty',
                'status' => 'open',
                'remarks' => 'Penalty for ' . $account->customer . ' of ₦' . number_format($interest, 2) . " On " . $account->plan . " account.",
                'debit' => 0,
                'credit' => $interest,
                'amount' => $interest * -1,
                'requires_approval' => $approval,
                'approved' => false,
                'posted' => false,
                'created_by' => $request->user()->name,
                'branch' => $request->user()->branch,
                'batch_number' => $otp
            ]);


            $sep_commision = $request->amount * $plan->penalty * $plan->sep_commission;
            //create sales agent line
            // $comm_line = CommissionLines::create([
            //     'handler' => $account->handler,
            //     'amount' => $sep_commision,
            //     'description' => 'Commission for withdrawal of ₦' . number_format($request->amount, 2) . ' charged at ₦' . number_format($request->commission, 2) . ' for ' . $account->customer,
            //     'batch_number' => $batch_number,
            //     'payment_id' => $withdrawal->id,
            //     'transaction_type' => 'withdrawal',
            //     'disbursed' => false,
            //     'branch' => $request->user()->branch,
            //     'approved' => false,
            //     // 'transaction_type'=>'commission'
            // ]);

            $cust = Customer::where('id', $request->no)->first();

            $res = OtpCode::create([
                'code' => $otp,
                'user_id' => $request->user()->id
            ]);

            $msg = 'Dear Customer, use ' . $otp . ' as OTP to withdraw ' . number_format($request->amount, 0) . '';

            $resp = sendSMS($customer->phone, $msg);
            return response([
                'success' => true,
                "code" => $otp
            ]);
        }
    } else {
        if ($balance < $total_credit) {
            return response([
                "success" => false,
                "message" => "You do not have enough balance in this account to withdraw ₦" . number_format($request->amount) . "."
            ]);
        }
        //check pending withdrawal
        $pending_withdrawal = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'withdrawal')->sum('credit');
        $pending_charge = Payments::where('savings_account_id', $account->id)->where('status', 'pending')->where('transaction_type', 'charge')->sum('credit');

        if ($balance < ($total_credit + $pending_withdrawal + $pending_charge)) {
            return response([
                "success" => false,
                "message" => "Unable to process this withdrawal since customer has a pending withdrawal, this amount exceed the remaining balance."
            ]);
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
            'status' => 'open',
            'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->amount, 2) . " On " . $account->plan . " account.",
            'debit' => 0,
            'credit' => $request->amount,
            'amount' => $request->amount * -1,
            'requires_approval' => $approval,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name,
            'branch' => $request->user()->branch,
            'batch_number' => $otp
        ]);

        //create charge line
        $charge = Payments::create([
            'savings_account_id' => $account->id,
            'plan' => $account->plan,
            'customer_id' => $account->customer_id,
            'customer_name' => $account->customer,
            'transaction_type' => 'charge',
            'status' => 'open',
            'remarks' => 'Withdrawal from ' . $account->customer . ' of ₦' . number_format($request->commission, 2) . " On " . $account->plan . " account.",
            'debit' => 0,
            'credit' => $request->commission,
            'amount' => $request->commission * -1,
            'requires_approval' => $approval,
            'approved' => false,
            'posted' => false,
            'created_by' => $request->user()->name,
            'branch' => $request->user()->branch,
            'batch_number' => $otp
        ]);


        $sep_commision = $request->commission * $plan->sep_commission;
        //create sales agent line
        $comm_line = CommissionLines::create([
            'handler' => $account->handler,
            'amount' => $sep_commision,
            'description' => 'Commission for withdrawal of ₦' . number_format($request->amount, 2) . ' charged at ₦' . number_format($request->commission, 2) . ' for ' . $account->customer,
            'batch_number' => $batch_number,
            'transaction_type' => 'withdrawal',
            'payment_id' => $charge->id,
            'disbursed' => false,
            'branch' => $request->user()->branch,
            'approved' => false,
            // 'transaction_type'=>'commission'
        ]);
    }

    $cust = Customer::where('id', $request->no)->first();
    $res = OtpCode::create([
        'code' => $otp,
        'user_id' => $request->user()->id
    ]);

    $msg = 'Dear Customer, use ' . $otp . ' as OTP to withdraw ' . number_format($request->amount, 0);

    $resp = sendSMS($customer->phone, $msg);


    return response([
        "success" => true,
        "code" => $otp
    ]);
});

Route::middleware("auth:sanctum")->post("/create_account", function (Request $request) {

    $customer = Customer::where('id', $request->no)->first();
    $plan = Plans::where('id', $request->plan_id)->first();


    $name = $request->name;
    if ($name == null) {
        $name = $plan->name . ".";
    }
    $account = SavingsAccount::create([
        'customer_id' => $customer->id,
        'customer_number' => $customer->no,
        'pledge' => $request->pledge,
        'plans_id' => $plan->id,
        'name' => $name,
        'created_by' => "Admin",
        'active' => true,
        'branch' => 'test',
        'handler' => $customer->name,
        'customer' => $customer->name,
        'plan' => $plan->name
    ]);

    return response($account);
});

Route::middleware('auth:sanctum')->get("/balance/{id}", function ($id) {
    $acc = SavingsAccount::where('customer_id', $id)->where('name', 'Regular')->first();

    $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->where('transaction_type', 'savings')->sum('amount');

    $saving_accounts = array();
    $saving_accounts['confirmed'] = $confirmed_transaction;
    return response($saving_accounts);
});

Route::middleware('auth:sanctum')->get("/account_plan/{no}", function ($no) {
    $account = SavingsAccount::where('id', $no)->first();
    $plan = Plans::where('id', $account->plans_id)->first();
    return response($plan);
});

Route::middleware("auth:sanctum")->get("/account_balance/{id}", function ($id) {
    $balance = Payments::where('savings_account_id', $id)->sum('amount') - 1000;
    $account = SavingsAccount::where('id', $id)->first();
    $plan = Plans::where('id', $account->plans_id)->first();
    $response = array();

    return response([
        "plan" => $plan,
        "balance" => $balance,
        "balance_text" => "₦" . number_format($balance, 0)
    ]);
});

Route::middleware("auth:sanctum")->get("/regular/{id}", function ($id) {

    $acc = SavingsAccount::where('customer_id', $id)->where('name', 'Regular')->first();

    $confirmed_transaction = Payments::where('savings_account_id', $acc->id)->where('status', 'confirmed')->where('transaction_type', 'savings')->sum('amount');

    $saving_accounts = array();
    $saving_accounts['confirmed'] = $confirmed_transaction;
    return response($saving_accounts);
});

Route::middleware("auth:sanctum")->get("/dashboard", function (Request $request) {
    Carbon::setWeekStartsAt(Carbon::MONDAY);
    Carbon::setWeekEndsAt(Carbon::SUNDAY);
    $resp = array();
    //get total months 
    $todays_sales = Payments::where('status', 'confirmed')->where(function ($q) {
        $q->where('transaction_type', 'savings')
            ->orWhere('transaction_type', 'registration');
    })->where('created_by', $request->user()->name)->whereDate('created_at', Carbon::today())->sum('amount');
    $weekly_sales = Payments::where('status', 'confirmed')->where(function ($q) {
        $q->where('transaction_type', 'savings')
            ->orWhere('transaction_type', 'registration');
    })->where('created_by', $request->user()->name)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    $monthly_sales = Payments::where('status', 'confirmed')->where(function ($q) {
        $q->where('transaction_type', 'savings')
            ->orWhere('transaction_type', 'registration');
    })->where('created_by', $request->user()->name)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
    $pending_collections = Payments::where('status', 'pending')->where('created_by', $request->user()->name)->where(function ($q) {
        $q->where('transaction_type', 'savings')
            ->orWhere('transaction_type', 'registration');
    })->sum('amount');

    $todays_withdrawals = Payments::where('status', 'confirmed')->where('transaction_type', 'withdrawal')->where('created_by', $request->user()->name)->whereDate('created_at', Carbon::today())->sum('amount');
    $weekly_withdrawals = Payments::where('status', 'confirmed')->where('transaction_type', 'withdrawal')->where('created_by', $request->user()->name)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
    $monthly_withdrawals = Payments::where('status', 'confirmed')->where('transaction_type', 'withdrawal')->where('created_by', $request->user()->name)->whereMonth('created_at', Carbon::now()->month)->sum('amount');
    $pending_withdrawals = Payments::where('status', 'pending')->where('transaction_type', 'withdrawal')->where('created_by', $request->user()->name)->sum('amount');


    //$pending_withdrawals = Payments::where('transaction_type', 'withdrawal')->where('created_by', $request->user()->name)->whereDate('created_at', Carbon::today())->sum('amount');

    $shortage_allowance = CommissionLines::where('transaction_type', 'savings')->where('approved', true)->where('handler', $request->user()->name)->sum('amount');
    $withdrawal_allowance = CommissionLines::where('handler', $request->user()->name)
        ->where('transaction_type', 'withdrawal')->where('approved', true)->sum('amount');
    $reg_commissions = CommissionLines::where('handler', $request->user()->name)
        ->where('transaction_type', 'registration')->sum('amount');

    $pending_ = CommissionLines::where('handler', $request->user()->name)
        ->where('transaction_type', 'registration')->sum('amount');

    $a_c = Customer::where('handler', $request->user()->name)->get();
    $active_customers = Payments::where('created_by', $request->user()->name)->distinct()->get(['customer_id']);

    $total_customers = Customer::where('handler', $request->user()->name)->count();
    $new_customers = Customer::where('handler', $request->user()->name)->whereMonth('created_at', Carbon::now()->month)->count();
    $resp['total_customers'] = $total_customers;
    $resp['new_customers'] = $new_customers;
    $resp['active_customers'] = count($active_customers);
    $resp['today_sales'] = number_format($todays_sales, 0);
    $resp['weekly_sales'] = number_format($weekly_sales, 0);
    $resp['monthly_sales'] = number_format($monthly_sales, 0);
    $resp['monthly_withdrawals'] = number_format($monthly_sales, 0);
    $resp['pending_collections'] = number_format($pending_collections, 0);
    $resp['today_withdrawal'] = number_format(($todays_withdrawals * -1), 0);
    $resp['weekly_withdrawal'] = number_format(($weekly_withdrawals * -1), 0);
    $resp['monthly_withdrawals'] = number_format(($monthly_withdrawals * -1), 0);
    $resp['pending_withdrawal'] = number_format(($pending_withdrawals * -1), 0);

    $resp['savings_commission'] = number_format($shortage_allowance, 2);
    $resp['withdrawal_commission'] = number_format($withdrawal_allowance, 2);
    $resp['loan_commision'] = number_format(0, 2);
    $resp['registration_commissions'] = number_format($reg_commissions, 2);
    $resp['user'] =  $request->user();

    return response($resp);
});

function get_customer_number()
{
    $customers = Customer::all();
    $no = count($customers) + 1;
    return str_pad($no, 6, '0', STR_PAD_LEFT);
}

function sendSMS($phone, $message)
{
    Log::warning('sending');
    $url = 'http://pro.strongsmsportal.com/api/?username=neodream&password=Prayer12&message=' . $message . '&sender=Reliance&mobiles=234' . formatNumber($phone);

    $response =  Http::get($url)->json();

    Log::info($response);

    return $response;
}

function formatNumber($phone)
{
    return ltrim($phone, $phone[0]);
}
