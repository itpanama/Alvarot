<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTicketsMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('comments');
            $table->integer('user_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->index(['ticket_id']);

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
        Schema::table('tickets_messages', function (Blueprint $table) {
            $table->dropForeign('tickets_messages_ticket_id_foreign');
        });

        Schema::dropIfExists('tickets_messages');
    }
}
