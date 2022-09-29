<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\FieldTeam;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IosController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TargetsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserDetails;
use App\Http\Controllers\Withdrawal;
use App\Http\Controllers\WithdrawalsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->get('/home', [HomeController::class, 'index'])->name("home");

Auth::routes();

Route::middleware(['auth'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/team', [UserDetails::class, 'index'])->name('team.list');
    Route::get('/team/create', [UserDetails::class, 'create'])->name('team.create');
    Route::post('/team/store', [UserDetails::class, 'store'])->name('team.store');
    Route::get('/team/edit', [UserDetails::class, 'edit'])->name('team.edit');

    Route::get('/branch_targets', [TargetsController::class, 'index'])->name('targets.list');
    Route::get('/branch_targets/create/{id}', [TargetsController::class, 'create'])->name('targets.create');
    Route::post('/branch_targets/store', [TargetsController::class, 'store'])->name('targets.store');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/ios/customers', [IosController::class, 'index'])->name('ios.customers');
    Route::get('/ios/customer/{id}', [IosController::class, 'customer'])->name('ios.customer');
    Route::post('/ios/create_plan', [IosController::class, 'create_plan'])->name('create_plan');
    Route::get('/ios/payment/{id}', [IosController::class, 'make_payment'])->name('ios.payment');
    Route::get('/ios/withdrawal/{id}', [IosController::class, 'make_withdrawal'])->name('ios.withdrawal');
    Route::post('/ios/pay', [IosController::class, 'pay'])->name('pay');
    Route::post('/ios/withdraw', [IosController::class, 'withdraw'])->name('ios.withdraw');



    Route::get('/contributions', [TransactionsController::class, 'index'])->name('contributions.list');
    Route::get('/contributions/create', [TransactionsController::class, 'create'])->name('contributions.create');

    Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('withdrawals.list');
    Route::get('/withdrawals/create', [WithdrawalsController::class, 'create'])->name('withdrawals.create');

    // Route::get('/customers', [MembersController::class, 'index'])->name('customers.list');
    // Route::get('/customers/create', [MembersController::class, 'create'])->name('customers.create');
    // Route::post('/customers/store', [MembersController::class, 'store'])->name('customers.store');
    // Route::get('/customers/show/{id}', [MembersController::class, 'show'])->name('customers.show');
    // Route::get('/customers/contribution/{id}', [MembersController::class, 'contribution'])->name('customers.contribution');
    // Route::get('/customers/contribute', [MembersController::class, 'contribute'])->name('customers.contribute');

    Route::get('/office/index', [OfficeController::class, 'index'])->name('office.list');
    Route::get('/office/reconcile/{id}', [OfficeController::class, 'reconcile'])->name('office.reconcile');
    Route::post('/office/reconciliation', [OfficeController::class, 'receive'])->name('office.receive');
    Route::get('/office/withdrawal_list/{id}', [OfficeController::class, 'withdrawal_list'])->name('office.withdrawal_list');

    Route::post('/office/reconcile_withdrawal', [OfficeController::class, 'disburse'])->name('office.disburse');
    Route::get('/office/reconcile_withdrawal/{id}', [OfficeController::class, 'recon_page'])->name('office.recon_page');

    Route::get('/loans/index', [LoanController::class, 'index'])->name('loans.list');
    Route::get('/office/loan_recon_list/{id}', [LoanController::class, 'loan_recon_list'])->name('office.loan_recon_list');
    Route::get('/office/commissions', [OfficeController::class, 'commissions'])->name('office.commissions');
    Route::get('/office/pof/{id}', [OfficeController::class, 'pay_on_field'])->name('office.pof');
    Route::post('/import', [UserDetails::class, 'uploadUsers'])->name('import');
    Route::post('/import_balances', [OfficeController::class, 'import_balances'])->name('import_balances');

    Route::post('/import_loans', [UserDetails::class, 'uploadLoans'])->name('import_loans');
    Route::post('/import_customers', [MembersController::class, 'uploadCustomers'])->name('import_customers');
    Route::get('/create_account', [OfficeController::class, 'import_accounts'])->name('import_accounts');
    Route::get('/reset_accounts', [OfficeController::class, 'reset_balances'])->name('reset_accounts');

    Route::get('/repayment', [OfficeController::class, 'save_loan_repayment'])->name('repayments');
    Route::get('/branch_formation', [OfficeController::class, 'branch_creation'])->name('branch_formation');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/customers', [SalesController::class, 'customers'])->name('sales.customers');
    Route::get('/customer/{id}', [SalesController::class, 'customer'])->name('sales.customer');
    Route::get('/loan/{id}', [SalesController::class, 'loan'])->name('sales.loan');
    Route::post('/loan', [SalesController::class, 'apply_loan'])->name('sales.apply_loan');
    Route::get('/repay/{id}', [SalesController::class, 'repay_loan'])->name('sales.repay_loan');
    Route::post('/loan_repayment', [SalesController::class, 'post_loan_repay'])->name('sales.post_loan_repay');

    Route::get('/collection/{id}', [SalesController::class, 'collection'])->name('sales.collection');
    Route::post('/pay', [SalesController::class, 'pay'])->name('sales.pay');
    Route::get('/withdrawal/{id}', [SalesController::class, 'withdrawal'])->name('sales.withdrawal');
    Route::post('/post_withdrawal', [SalesController::class, 'post_withdrawal'])->name('sales.post_withdrawal');
    Route::post('/verify', [SalesController::class, 'verify_withdrawal'])->name('sales.verify');
});

Route::get('/backend', [OfficeController::class, 'backend'])->name('backend');
Route::get('/sep/{name}', [OfficeController::class, 'seps'])->name('seps');
Route::get('/sep-customers/{name}', [OfficeController::class, 'customer_seps'])->name('customer_seps');
Route::get('/sep_customer/{id}', [OfficeController::class, 'customer'])->name('sep_customer');
Route::post('/change_phone', [OfficeController::class, 'change_phone'])->name('change_phone');
Route::post('/migrate_plan', [OfficeController::class, 'migrate_plan'])->name('migrate_plan');
Route::get('/loans', [SalesController::class, 'loans'])->name('sep_loans');




//Branch Managers
Route::get('/branch_loans', [BranchController::class, 'loans'])->name('branch_loans');
Route::get('/branch_loan/{id}', [BranchController::class, 'loan_card'])->name('loan_card');

Route::get('/delete_loan_payment/{id}', [OfficeController::class, 'delete_loan_payment'])->name('delete_loan_payment');
Route::get('/delete_saving_account/{id}', [OfficeController::class, 'delete_saving_account'])->name('delete_saving_account');
Route::get('/delete_payment/{id}', [OfficeController::class, 'delete_payment'])->name('delete_payment');

Route::get('/collection', [SalesController::class, 'show_collection'])->name('show_collection');

Route::get('/', [HomeController::class, 'index'])->name('home');
