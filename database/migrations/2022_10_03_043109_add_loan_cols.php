<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoanCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans', function (Blueprint $table) {
            // $table->boolean('branch_manager_approval')->boolean(false);
            // $table->string('branch_manager_remarks')->nullable();
            // $table->boolean('loan_officer_approval')->default(false);
            // $table->string('loan_officer_remarks')->nullable();
            // $table->boolean('legal_approval')->default(false);
            // $table->string('legal_remarks')->nullable();
            // $table->boolean('public_finance_approval')->default(false);
            // $table->string('public_finance_remarks')->nullable();     
            // $table->boolean('disbursed')->default(false);     
            // $table->boolean('public_finance')->default(false);
            // $table->boolean('direct')->default(false);
            // $table->boolean('legal')->default(false);   

            // $table->boolean('Cheque')->default(false);      
            // $table->boolean('CivilServantGuarantee')->default(false);      
            // $table->boolean('Guarantorship')->default(false);        
            // $table->boolean('Collateral')->default(false);   
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans;
', function (Blueprint $table) {
            //
        });
    }
}
