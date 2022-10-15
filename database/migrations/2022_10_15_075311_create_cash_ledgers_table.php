<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashLedgersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_ledgers', function (Blueprint $table) {
            $table->id();
            $table->date('report_date')->nullable();
            $table->string('branch')->nullable();
            $table->string('remarks')->nullable();
            $table->string('category')->nullable();
            $table->double('amount')->default(0);
            $table->boolean('outward')->default(false);
            $table->boolean('inward')->default(false);
            $table->string('to')->nullable();
            $table->string('from')->nullable();
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
        Schema::dropIfExists('cash_ledgers');
    }
}
