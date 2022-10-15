<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->nullable();
            $table->double('opening_balance')->default(0);
            $table->double('Remmitance')->default(0);
            $table->double('CashInflow')->default(0);
            $table->double('CashOutflow')->default(0);
            $table->double('Expense')->default(0);
            $table->double('Withdrawals')->default(0);
            $table->double('LoanIssued')->default(0);
            $table->double('TotalCashIn')->default(0);
            $table->double('TotalCashOut')->default(0);
            $table->double('CashbookBalance')->default(0);
            $table->double('CashAtHand')->default(0);
            $table->double('Shortage')->default(0);
            $table->string('branch')->default(0);
            $table->string('admin_remarks')->default(0);
            $table->string('operations_head_remarks')->default(0);
            
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
        Schema::dropIfExists('cash_summaries');
    }
}
