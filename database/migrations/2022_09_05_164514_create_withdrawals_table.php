<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->string('name');
            $table->decimal('amount');
            $table->string('handler');
            $table->string('branch');
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('confirmed_by')->nullable();
            $table->string('document_number');
            $table->decimal('comission_amount');
            $table->boolean('commission_paid')->default(false);
            $table->boolean('request_approval')->default(false);
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
        Schema::dropIfExists('withdrawals');
    }
}
