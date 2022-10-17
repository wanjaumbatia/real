<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use League\CommonMark\Extension\Table\Table;

class CreateCashFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_flows', function (Blueprint $table) {
            $table->id();
            $table->string('branch')->nullable();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->string('debit')->nullable();
            $table->string('credit')->nullable();
            $table->double('amount')->nullable();
            $table->string('description')->nullable();
            $table->string('status')->nullable();
            $table->string('remarks')->nullable();

            $table->string('created_by')->nullable();
            $table->string('confirmed_by')->nullable();
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
        Schema::dropIfExists('cash_flows');
    }
}
