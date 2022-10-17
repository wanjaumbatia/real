<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\FieldTeam;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IosController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MdsController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TargetsController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\UserDetails;
use App\Http\Controllers\Withdrawal;
use App\Http\Controllers\WithdrawalsController;
use App\Models\Branch;
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

Route::middleware(['auth'])->name('branch')->prefix('branch')->group(function () {
    Route::get('/clients', [BranchController::class, 'all_clients'])->name('all_clients');
    Route::get('/customer/{id}', [BranchController::class, 'customer'])->name('customer');
    Route::get('/sales_executives', [BranchController::class, 'sales_executives'])->name('sales_executives');

    Route::get('/sep_ollection', [BranchController::class, 'sep_ollection'])->name('sep_ollection');
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

    Route::get('/loans/index', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/process', [LoanController::class, 'request'])->name('loans.requests');
    Route::get('/loans/process/{id}', [LoanController::class, 'processing_loan_card'])->name('loans.process_card');
    Route::get('/request_card/{id}', [LoanController::class, 'request_card'])->name('loans.request_card');

    Route::get('/loan_repay_ledger_single/{id}', [LoanController::class, 'loan_repay_ledger_single'])->name('loan_repay_ledger_single');
    Route::get('/loan_repay_ledger', [LoanController::class, 'loan_repay_ledger'])->name('loan_repay_ledger');
    Route::get('/loan_ledger', [LoanController::class, 'loan_ledger'])->name('loan_ledger');
    Route::get('/charge_date', [LoanController::class, 'charge_date'])->name('charge_date');

    Route::get('/office/commissions', [OfficeController::class, 'commissions'])->name('office.commissions');
    Route::get('/office/pof/{id}', [OfficeController::class, 'pay_on_field'])->name('office.pof');
    Route::post('/import', [UserDetails::class, 'uploadUsers'])->name('import');
    Route::post('/import_balances', [OfficeController::class, 'import_balances'])->name('import_balances');

    Route::post('/import_loans', [UserDetails::class, 'uploadLoans'])->name('import_loans');
    Route::post('/import_customers', [MembersController::class, 'uploadCustomers'])->name('import_customers');
    //Route::get('/create_account', [OfficeController::class, 'import_accounts'])->name('import_accounts');
    Route::get('/reset_accounts', [OfficeController::class, 'reset_balances'])->name('reset_accounts');

    Route::get('/repayment', [OfficeController::class, 'save_loan_repayment'])->name('repayments');
    Route::get('/branch_formation', [OfficeController::class, 'branch_creation'])->name('branch_formation');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/withdrawal_list', [SalesController::class, 'withdrawal_list'])->name('sales.withdrawal_list');
    Route::get('/loan_repayment_logs', [SalesController::class, 'loan_repayment_logs'])->name('sales.loan_repayment_logs');

    Route::get('/customers', [SalesController::class, 'customers'])->name('sales.customers');
    Route::get('/customer/{id}', [SalesController::class, 'customer'])->name('sales.customer');
    Route::get('/loan/{id}', [SalesController::class, 'loan'])->name('sales.loan');
    Route::post('/loan', [SalesController::class, 'apply_loan'])->name('sales.apply_loan');
    Route::get('/repay/{id}', [SalesController::class, 'repay_loan'])->name('sales.repay_loan');
    Route::post('/loan_repayment', [SalesController::class, 'post_loan_repay'])->name('sales.post_loan_repay');
    Route::get('/payments_by_date', [SalesController::class, 'payments_by_date'])->name('payments_by_date');

    Route::get('/collection/{id}', [SalesController::class, 'collection'])->name('sales.collection');
    Route::post('/pay', [SalesController::class, 'pay'])->name('sales.pay');
    Route::get('/withdrawal/{id}', [SalesController::class, 'withdrawal'])->name('sales.withdrawal');
    Route::post('/post_withdrawal', [SalesController::class, 'post_withdrawal'])->name('sales.post_withdrawal');
    Route::post('/verify', [SalesController::class, 'verify_withdrawal'])->name('sales.verify');
    Route::post('/create_account/{id}', [SalesController::class, 'create_plan'])->name('sales.create_plan');
    Route::get('/customer_balances', [SalesController::class, 'customer_balances'])->name('customer_balances');

    //Branch Managers
    Route::get('/branch_loans', [BranchController::class, 'loans'])->name('branch_loans');
    Route::get('/pending_branch_loans', [BranchController::class, 'pending_branch_loans'])->name('pending_branch_loans');
    Route::get('/processing_branch_loans', [BranchController::class, 'processing_branch_loans'])->name('processing_branch_loans');
    Route::get('/processing_branch_loan_card/{id}', [BranchController::class, 'processing_loan_card'])->name('processing_branch_loan_card');

    Route::post('/save_security/{id}', [BranchController::class, 'save_security'])->name('save_security');

    Route::get('/branch_loan/{id}', [BranchController::class, 'loan_card'])->name('loan_card');
    Route::post('/branch_upload_forms/{id}', [BranchController::class, 'upload_forms'])->name('branch_upload_forms');
    Route::post('/branch_approve_loan/{id}', [BranchController::class, 'branch_approve_loan'])->name('branch_approve_loan');
    Route::get('/approved_branch_loans', [BranchController::class, 'approved_branch_loans'])->name('approved_branch_loans');
    Route::get('/disburse/{id}', [BranchController::class, 'disburse_loan'])->name('disburse_loan');

    Route::get('/statement/{id}', [SalesController::class, 'statement'])->name('statement');
    Route::get('/loan_status/{id}', [SalesController::class, 'loan_card'])->name('sales.loan_card');
    Route::get('/recon_statement', [OfficeController::class, 'recon_statement'])->name('recon_statement');
    Route::get('/recon_per_ref/{id}', [OfficeController::class, 'recon_per_ref'])->name('recon_per_ref');
    Route::get('/recon_report_by_date', [OfficeController::class, 'recon_report_by_date'])->name('recon_report_by_date');
    Route::get('/recon_val/{date}', [OfficeController::class, 'recon_val'])->name('recon_val');
    Route::get('/charge_interest', [LoanController::class, 'charge_interest'])->name('charge_interest');
    //Route::get('/loan_repayment', [LoanController::class, 'loan_repayment'])->name('loan_repayment');
    Route::post('/loan_officer_approval/{id}', [LoanController::class, 'loan_officer_approval'])->name('loan_officer_approval');
    Route::get('/under_processing', [LoanController::class, 'under_processing'])->name('loans.under_processing');

    Route::post('/branch_reject_loan/{id}', [BranchController::class, 'branch_reject_loan'])->name('branch_reject_loan');


    Route::get('/backend', [OfficeController::class, 'backend'])->name('backend');
    Route::get('/sep/{name}', [OfficeController::class, 'seps'])->name('seps');
    Route::get('/sep-customers/{name}', [OfficeController::class, 'customer_seps'])->name('customer_seps');
    Route::get('/sep_customer/{id}', [OfficeController::class, 'customer'])->name('sep_customer');
    Route::post('/change_phone', [OfficeController::class, 'change_phone'])->name('change_phone');
    Route::post('/change_name', [OfficeController::class, 'change_name'])->name('change_name');
    Route::post('/migrate_plan', [OfficeController::class, 'migrate_plan'])->name('migrate_plan');
    Route::get('/loans', [SalesController::class, 'loans'])->name('sep_loans');
    Route::post('/change_amount', [OfficeController::class, 'change_amount'])->name('change_amount');
    Route::post('/change_loan_amount', [OfficeController::class, 'change_loan_amount'])->name('change_loan_amount');
    Route::post('/change_plan', [OfficeController::class, 'change_plan'])->name('change_plan');
    Route::post('/handler_change/{id}', [OfficeController::class, 'handler_change'])->name('handler_change');
    Route::get('/upload_accounts', [BranchController::class, 'upload_accounts'])->name('upload_accounts');
    Route::post('/withdrawal_fix', [SalesController::class, 'withdrawal_fix'])->name('withdrawal_fix');
    Route::get('/payments_by_date_branch', [OfficeController::class, 'payments_by_date'])->name('payments_by_date');
    Route::get('/fix_withdrawals_office', [OfficeController::class, 'fix_withdrawals'])->name('fix_withdrawals_office');

    Route::get('/delete_loan_payment/{id}', [OfficeController::class, 'delete_loan_payment'])->name('delete_loan_payment');
    Route::get('/delete_saving_account/{id}', [OfficeController::class, 'delete_saving_account'])->name('delete_saving_account');
    Route::get('/delete_payment/{id}', [OfficeController::class, 'delete_payment'])->name('delete_payment');

    Route::get('/collection', [SalesController::class, 'show_collection'])->name('show_collection');
    Route::get('/reconciled/{date}', [SalesController::class, 'show_recon'])->name('show_recon');
    Route::get('/reconciled_group', [SalesController::class, 'show_recon_group'])->name('show_recon_group');
    Route::get('/reconciliation/{reference}', [SalesController::class, 'show_recon_data'])->name('show_recon_data');
    Route::get('/withdrawal_by_date/{date}', [SalesController::class, 'withdrawal_by_date'])->name('show_collection');
    Route::get('/reg_fee_collection', [SalesController::class, 'reg_fee_collection'])->name('reg_fee_collection');

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [OfficeController::class, 'search_customer'])->name('search');
    Route::post('/make_deposit', [OfficeController::class, 'make_deposit'])->name('make_deposit');

    Route::post('/post_withdrawal1', [OfficeController::class, 'post_withdrawal'])->name('post_withdrawal');

    Route::post('/import_loans_new', [LoanController::class, 'ImportLoans'])->name('import_loans_new');
    Route::get('/new_customer', [SalesController::class, 'new_customer'])->name('new_customer');
    Route::post('/save_customer', [SalesController::class, 'save_customer'])->name('save_customer');

    Route::get('/fix_reg_fee', [OfficeController::class, 'fix_reg_fee'])->name('fix_reg_fee');

    Route::get('/fix_missing_transactions', [HomeController::class, 'fix_missing_transactions'])->name('fix_missing_transactions');

    Route::get('/new_real_saving', [SalesController::class, 'new_real_invest'])->name('new_real_invest');
    Route::get('/real_invest_list', [SalesController::class, 'real_invest_list'])->name('real_invest_list');
    Route::Post('/post_real_invest', [SalesController::class, 'post_real_invest'])->name('post_real_invest');

    Route::get('/active_loans', [LoanController::class, 'active_loans'])->name('loans.active_loans');
    Route::get('/expired_loans', [LoanController::class, 'expired_loans'])->name('loans.expired_loans');
    Route::get('/bad_loans', [LoanController::class, 'bad_loans'])->name('loans.bad_loans');
    Route::get('/loan_card/{id}', [LoanController::class, 'loan_card'])->name('loans.loan_card');
    Route::get('/loans_by_branch', [LoanController::class, 'loans_by_branch'])->name('loans.loans_by_branch');
    Route::get('/repay_test/{id}', [LoanController::class, 'repay_test'])->name('loans.repay_test');

    Route::get('/branch_active_loans', [BranchController::class, 'active_loans'])->name('branch.active_loans');
    Route::get('/branch_expired_loans', [BranchController::class, 'expired_loans'])->name('branch.expired_loans');
    Route::get('/branch_bad_loans', [BranchController::class, 'bad_loans'])->name('branch.bad_loans');
    Route::get('/branch_loan_status_summary', [BranchController::class, 'loan_status_summary'])->name('branch_loan_status_summary');
    Route::get('/sales_loan_status_summary', [SalesController::class, 'loan_status_summary'])->name('sales_loan_status_summary');

    Route::get('/loan_status_summary', [LoanController::class, 'loan_status_summary'])->name('\loan_status_summary');
    Route::get('/loan_review/{id}', [LoanController::class, 'loan_review'])->name('loans.loan_review');
    Route::get('/loan_by_sep', [LoanController::class, 'loan_by_sep'])->name('loans.loan_by_sep');
    Route::post('/save_review', [LoanController::class, 'save_review'])->name('loans.save_review');

    Route::get('/branch_loan_review/{id}', [BranchController::class, 'loan_review'])->name('branch.loan_review');
    Route::get('/branch_loan_by_sep', [BranchController::class, 'loan_by_sep'])->name('branch.loan_by_sep');
    Route::post('/branch_save_review', [BranchController::class, 'save_review'])->name('branch.save_review');

    Route::get('/add_cash_summary', [OfficeController::class, 'add_cash_summary'])->name('add_cash_summary');
    Route::post('/save_summary', [OfficeController::class, 'save_summary'])->name('save_summary');

    Route::get('/reviews', [SalesController::class, 'reviews'])->name('sales.reviews');
    Route::get('/sales_review/{id}', [SalesController::class, 'sales_review'])->name('sales_review');
    Route::post('/move_saving', [BranchController::class, 'move_saving'])->name('branch.move_saving');

    Route::get('/admin_expenses', [OfficeController::class, 'expenses_list'])->name('office.expenses_list');
    Route::get('/new_expense', [OfficeController::class, 'new_expense'])->name('office.new_expense');
    Route::get('/expense_types', [OfficeController::class, 'expense_types'])->name('operations.expense_types');
    Route::get('/new_expense_types', [OfficeController::class, 'new_expense_types'])->name('operations.new_expense_types');

    Route::post('/add_expense_type', [OfficeController::class, 'add_expense_type'])->name('operations.add_expense_type');

    Route::get('/new_real_invest', [SalesController::class, 'new_real_invest'])->name('sales.new_real_invest');
    Route::post('/create_real_invest', [SalesController::class, 'create_real_invest'])->name('sales.create_real_invest');
    Route::get('/real_invest_list', [SalesController::class, 'real_invest_list'])->name('sales.real_invest_list');
    Route::get('/pending_real_invest', [SalesController::class, 'pending_real_invest'])->name('sales.pending_real_invest');
    Route::get('/sales_withdrawn_real_invest', [SalesController::class, 'withdrawn_real_invest'])->name('sales.withdrawn_real_invest');

    Route::get('/real_invest_pending', [OfficeController::class, 'real_invest_pending'])->name('real_invest_pending');
    Route::get('/active_real_invest', [OfficeController::class, 'active_real_invest'])->name('active_real_invest');
    Route::get('/withdrawn_real_invest', [OfficeController::class, 'withdrawn_real_invest'])->name('withdrawn_real_invest');
    Route::get('/confirm_real_invest/{id}', [OfficeController::class, 'confirm_real_invest'])->name('confirm_real_invest');

    Route::get('/import_expense_codes', [MdsController::class, 'import_expense_codes'])->name('import_expense_codes');
    Route::post('/post_expense_code_excel', [MdsController::class, 'post_expense_code_excel'])->name('post_expense_code_excel');
    Route::post('/post_expense', [OfficeController::class, 'post_expense'])->name('post_expense');
    Route::get('/branch_cash_summary', [OfficeController::class, 'cash_summary'])->name('branch.cash_summary');

    Route::get('/new_cash_summary', [OfficeController::class, 'new_cash_summary'])->name('new_cash_summary');
    Route::post('/post_cash_summary', [OfficeController::class, 'post_cash_summary'])->name('post_cash_summary');
    
    Route::get('/import_expenses_codes', [MdsController::class, 'import_expense_codes'])->name('import_expense_codes');
    Route::get('/generate_prev_summary', [MdsController::class, 'generate_prev_summary'])->name('generate_prev_summary');
    Route::get('/pending_expenses', [OfficeController::class, 'PendingExpenses'])->name('ops.PendingExpenses');
    Route::get('/approve_expenses/{id}', [OfficeController::class, 'ApproveExpenses'])->name('ops.ApproveExpenses');

    Route::post('/import_real_invest', [OfficeController::class , 'import_real_invest'])->name('import_real_invest');
    Route::get('/confirmed_cashflow', [OfficeController::class, 'confirmed_cashflow'])->name('confirmed_cashflow');
    Route::get('/new_cashflow', [OfficeController::class, 'new_cashflow'])->name('new_cash_flow');
    Route::post('/post_cashflow', [OfficeController::class, 'post_cashflow'])->name('post_cashflow');

    Route::get('/remmittance', [OfficeController::class, 'remittance'])->name('remittance');
    Route::get('/cash_summary_withdrawals', [OfficeController::class, 'cash_summary_withdrawals'])->name('cash_summary_withdrawals');
});
