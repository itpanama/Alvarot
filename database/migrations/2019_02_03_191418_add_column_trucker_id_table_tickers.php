<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTruckerIdTableTickers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('trucker_id')->unsigned()->nullable()->after('user_assigned_date');

            $table->foreign('trucker_id')
                ->references('id')
                ->on('truckers')
                ->onDelete('cascade');

            $table->index(['trucker_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('tickets_trucker_id_foreign');
            $table->dropColumn('trucker_id');
        });
    }
}
