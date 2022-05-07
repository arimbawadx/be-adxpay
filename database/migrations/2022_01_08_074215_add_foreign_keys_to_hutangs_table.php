<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToHutangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hutangs', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hutangs', function (Blueprint $table) {
            $table->dropForeign('hutangs_customer_id_foreign');
        });
    }
}
