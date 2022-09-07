<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('branch')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('sales_executive')->default(false);
            $table->boolean('branch_manager')->default(false);
            $table->boolean('office_admin')->default(false);
            $table->boolean('assistant_manager')->default(false);
            $table->boolean('operations_manager')->default(false);
            $table->boolean('admin')->default(false);

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
        Schema::dropIfExists('users');
    }
}
