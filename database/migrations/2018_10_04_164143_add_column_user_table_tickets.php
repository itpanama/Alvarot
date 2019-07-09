<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('user_id_assigned')->unsigned()->nullable()->after('comments');
            $table->foreign('user_id_assigned')->references('id')->on('users');
            $table->index(['user_id_assigned']);
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
            $table->dropForeign('tickets_user_id_assigned_foreign');
            $table->dropColumn('user_id_assigned');
        });
    }
}
