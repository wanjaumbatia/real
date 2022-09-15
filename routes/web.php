<?php

use App\Http\Controllers\FieldTeam;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OfficeController;
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

Route::get('/', function () {
    return view('welcome');
});

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
    Route::get('/contributions', [TransactionsController::class, 'index'])->name('contributions.list');
    Route::get('/contributions/create', [TransactionsController::class, 'create'])->name('contributions.create');

    Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('withdrawals.list');
    Route::get('/withdrawals/create', [WithdrawalsController::class, 'create'])->name('withdrawals.create');

    Route::get('/customers', [MembersController::class, 'index'])->name('customers.list');
    Route::get('/customers/create', [MembersController::class, 'create'])->name('customers.create');
    Route::post('/customers/store', [MembersController::class, 'store'])->name('customers.store');
    Route::get('/customers/show/{id}', [MembersController::class, 'show'])->name('customers.show');
    Route::get('/customers/contribution/{id}', [MembersController::class, 'contribution'])->name('customers.contribution');
    Route::get('/customers/contribute', [MembersController::class, 'contribute'])->name('customers.contribute');
    
    Route::get('/office/index', [OfficeController::class, 'index'])->name('office.list');
    Route::get('/office/reconcile/{id}', [OfficeController::class, 'reconcile'])->name('office.reconcile');
    Route::post('/office/reconciliation', [OfficeController::class, 'receive'])->name('office.receive');
    Route::get('/office/withdrawal_list/{id}', [OfficeController::class, 'withdrawal_list'])->name('office.withdrawal_list');
    
    Route::post('/office/reconcile_withdrawal', [OfficeController::class, 'disburse'])->name('office.disburse');
    Route::get('/office/reconcile_withdrawal/{id}', [OfficeController::class, 'recon_page'])->name('office.recon_page');

    Route::get('/loans/index', [LoanController::class, 'index'])->name('loans.list');
    Route::get('/office/loan_recon_list/{id}', [LoanController::class, 'loan_recon_list'])->name('office.loan_recon_list');
    Route::get('/office/commissions', [OfficeController::class, 'commissions'])->name('office.commissions');
});


Route::middleware(['auth'])->prefix('/customers')->group(function () {
    Route::get('/index', [MembersController::class, 'index'])->name('customer.index');
    Route::get('/show', [MembersController::class, 'show'])->name('customer.show');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
