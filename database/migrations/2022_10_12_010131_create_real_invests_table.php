<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealInvestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('real_invests', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('exit_date')->nullable();
            $table->string('plan_name')->default('Real Invest');
            $table->boolean('is_customer')->default(false);

            $table->integer('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('handler')->nullable();

            $table->double('amount')->default(0);
            $table->integer('duration')->default(0);
            $table->decimal('percentage')->default(0);
            $table->double('returns')->default(0);
            $table->string('status')->default('New');
            $table->boolean('withdrawn')->default(false);
            $table->dateTime('withdrawal_date')->nullable();
            $table->string('withdrawn_by')->nullable();
            
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
        Schema::dropIfExists('real_invests');
    }
}
