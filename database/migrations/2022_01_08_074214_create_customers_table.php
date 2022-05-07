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
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('profile', 225)->nullable();
            $table->integer('saldo')->nullable();
            $table->integer('point')->nullable();
            $table->string('username');
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('deleted')->default(false);
            $table->tinyInteger('verified')->nullable()->default(0);
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
