<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('no');
            $table->string('name');
            $table->string('phone');
            $table->string('town')->nullable();
            $table->string('gender');
            $table->date('dob')->nullable();
            $table->string('address');
            $table->string('handler');            
            $table->string('bank')->nullable();
            $table->string('bankacc')->nullable();
            $table->string('business')->nullable();
            $table->boolean('posted')->default(false);
            $table->string('branch');
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
