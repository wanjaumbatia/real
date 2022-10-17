<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCloseLoanCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loans_models', function (Blueprint $table) {
            $table->string('loan_number')->nullable();
            $table->boolean('closed')->default(false);
            $table->string('close_remarks')->nullable();
            $table->string('closed_by')->nullable();
            $table->boolean('stop_interest')->default(false);
            $table->string('status_change_remarks')->nullable();
            $table->string('status_changed_date')->date();
            $table->string('changed_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans_models', function (Blueprint $table) {
            //
        });
    }
}
