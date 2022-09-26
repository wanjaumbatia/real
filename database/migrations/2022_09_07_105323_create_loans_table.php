<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->string('name');
            $table->unsignedBigInteger('customer_id');
            $table->dateTime('application_date')->nullable(now());
            $table->double('amount');
            $table->double('paid')->nullable();
            $table->decimal('interest_percentage');
            $table->integer('duration');
            $table->double('current_savings');
            $table->string('handler')->nullable();
            $table->string('purpose')->nullable();
            $table->string('status')->default('application');
            $table->string('remarks')->nullable();
            $table->boolean('posted')->default(false);
            $table->dateTime('date_posted')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
