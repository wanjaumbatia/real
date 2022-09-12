<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->boolean('default');
            $table->double('charge');
            $table->boolean('allow_multiple');
            $table->boolean('outward')->default(false);
            $table->integer('duration')->default(0);
            $table->double('reimbursement');
            $table->boolean('active')->default(true);
            $table->double('sep_commission');
            $table->double('penalty');
            $table->string('create_by');
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
        Schema::dropIfExists('plans');
    }
}
