<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMutationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mutations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->string('jenis_transaksi');
            $table->string('bukti_transfer')->nullable();
            $table->integer('jumlah_deposit')->nullable();
            $table->string('code')->nullable();
            $table->string('phone')->nullable();
            $table->string('idcust')->nullable();
            $table->string('status');
            $table->bigInteger('trxid_api')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('mutations');
    }
}
