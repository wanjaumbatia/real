<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRepaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->integer('loan_number');
            $table->string('no');
            $table->string('name');
            $table->decimal('amount');
            $table->string('handler');
            $table->string('branch');
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('confirmed_by')->nullable();
            $table->string('document_number');
            $table->boolean('posted')->default(false);
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
        Schema::dropIfExists('loan_repayments');
    }
}
