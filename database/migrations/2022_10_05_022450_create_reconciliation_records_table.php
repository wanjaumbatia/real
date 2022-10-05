<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconciliationRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciliation_records', function (Blueprint $table) {
            $table->id();
            $table->string('handler');
            $table->string('reconciled_by');
            $table->double('expected');
            $table->double('submited');
            $table->boolean('shortage');            
            $table->string('reconciliation_reference');
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
        Schema::dropIfExists('reconciliation_records');
    }
}
