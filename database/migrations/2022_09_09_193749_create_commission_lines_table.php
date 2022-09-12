<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commission_lines', function (Blueprint $table) {
            $table->id();
            $table->string('handler');
            $table->double('amount');
            $table->string('description');
            $table->string('batch_number');
            $table->unsignedBigInteger('payment_id');
            $table->boolean('disbursed');
            $table->boolean('approved')->default(false);
            $table->string('transaction_type')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('branch');

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
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
        Schema::dropIfExists('commission_lines');
    }
}
