<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRepaymentModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayment_models', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_number');
            $table->string('name');
            $table->double('amount');
            $table->string('handler');
            $table->string('branch');
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('confirmed_by')->nullable();
            $table->string('document_number')->nullable();
            $table->boolean('posted')->default(false);
            
            $table->string('reconciliation_reference')->nullable();
            $table->string('reconciled_by')->nullable();
            $table->boolean('admin_reconciled')->default(false);
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
        Schema::dropIfExists('loan_repayment_models');
    }
}
