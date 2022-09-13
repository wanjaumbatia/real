<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('no')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('town')->nullable();
            $table->string('gender')->nullable();
            $table->string('address');
            $table->string('handler');    
            $table->string('dob')->nullable();    
            $table->string('business')->nullable(Carbon::now());
            $table->boolean('posted')->default(false);
            $table->string('branch');
            $table->string('created_by');
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
        Schema::dropIfExists('customers');
    }
}
