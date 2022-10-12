<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanLedgerEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_model_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('customer')->nullable();
            $table->string('handler')->nullable();
            $table->string('branch')->nullable();
            $table->string('remarks')->nullable();
            $table->double('debit')->default(0);
            $table->double('credit')->default(0);
            $table->double('amount')->default(0);
            
            $table->foreign('loan_model_id')
                ->references('id')
                ->on('loans_models')
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
        Schema::dropIfExists('loan_ledger_entries');
    }
}
