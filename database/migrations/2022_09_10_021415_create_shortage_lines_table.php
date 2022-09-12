<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShortageLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shortage_lines', function (Blueprint $table) {
            $table->id();
            $table->string('sales_executive');
            $table->double('expected_amount');
            $table->double('give_amount');
            $table->double('short');
            $table->string('reference');
            $table->boolean('cleared');
            $table->string('office_admin');
            $table->string('description')->nullable();
            $table->boolean('reported')->default(false);
            $table->boolean('resolved')->default(false);
            $table->string('branch');
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('shortage_lines');
    }
}
