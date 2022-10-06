<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans_models', function (Blueprint $table) {
            $table->id();
            $table->dateTime('application_date')->nullable(now());
            $table->dateTime('start_date')->nullable(now());
            $table->dateTime('exit_date')->nullable(now());
            $table->dateTime('next_charge_date')->nullable();
            $table->boolean('disbursed')->default(false);
            $table->string('customer');
            $table->unsignedBigInteger('customer_id');
            $table->string('username')->nullable();
            $table->string('handler');
            $table->string('branch')->nullable();
            $table->double('loan_amount');
            $table->double('percentage')->nullable();
            $table->integer('duration')->nullable();
            $table->string('loan_status')->default('application');            
            $table->string('purpose')->nullable();          
            $table->string('remarks')->nullable();

            $table->double('total_interest')->nullable();
            $table->double('total_interest_paid')->nullable();

            $table->double('monthly_interest')->nullable();
            $table->double('monthly_interest_paid')->nullable();

            $table->double('monthly_principle')->nullable();
            $table->double('monthly_principle_paid')->nullable();
            $table->double('total_monthly_payment')->nullable();

            $table->double('total_monthly_paid')->nullable();
            $table->double('total_monthly_balance')->nullable();

            $table->double('total_amount_paid')->nullable();
            $table->double('capital_balance')->nullable();
            $table->double('total_balance')->nullable();

            $table->boolean('branch_manager_approval')->default(false);
            $table->string('branch_manager_remarks')->nullable();
            $table->boolean('loan_officer_approval')->default(false);
            $table->string('loan_officer_remarks')->nullable();
            $table->boolean('legal_approval')->default(false);
            $table->string('legal_remarks')->nullable();
            $table->boolean('public_finance_approval')->default(false);
            $table->string('public_finance_remarks')->nullable();

            $table->boolean('general_manager_approval')->default(false);
            $table->string('general_manager_remarks')->nullable();
            $table->boolean('md_approval')->default(false);
            $table->string('md_remarks')->nullable();

            $table->boolean('public_finance')->default(false);
            $table->boolean('direct')->default(false);
            $table->boolean('legal')->default(false);

            $table->boolean('Cheque')->default(false);
            $table->boolean('CivilServantGuarantee')->default(false);
            $table->boolean('Guarantorship')->default(false);
            $table->boolean('Collateral')->default(false);

            $table->string('disbursement_mode');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans_models');
    }
}
