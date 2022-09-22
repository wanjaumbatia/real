<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('savings_account_id');
            $table->string('plan')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name');
            $table->string('transaction_type'); //savings, withdrawals, loans, commision/charges
            $table->string('status');
            $table->string('remarks');
            $table->double('debit');
            $table->double('credit');
            $table->double('amount');
            $table->boolean('requires_approval')->default(false);
            $table->boolean('approved')->default(false);            
            $table->boolean('posted')->default(true);
            $table->string('created_by')->nullable();
            $table->boolean('cps')->default(false);
            $table->boolean('first_approver')->default(false);
            $table->boolean('second_approver')->default(false); 
            $table->boolean('sent_cps')->default(false);
            $table->string('batch_number');
            $table->string('branch')->nullable();
            $table->string('reference')->nullable(); 
            $table->boolean('reconciled')->default(false);    
                                   
            $table->foreign('savings_account_id')
                ->references('id')
                ->on('savings_accounts')
                ->onDelete('cascade');

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
        Schema::dropIfExists('payments');
    }
}
