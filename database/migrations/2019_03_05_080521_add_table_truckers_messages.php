<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTruckersMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('truckers_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->text('comments');
            $table->integer('user_id')->unsigned();
            $table->integer('trucker_id')->unsigned();
            $table->foreign('trucker_id')->references('id')->on('truckers')->onDelete('cascade');
            $table->index(['trucker_id']);

            $table->timestamps();
        });


        Schema::create('truckers_messages_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachment_name');
            $table->integer('attachment_size');
            $table->integer('trucker_message_id')->unsigned();
            $table->foreign('trucker_message_id')->references('id')->on('truckers_messages')->onDelete('cascade');
            $table->index(['trucker_message_id']);

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
        Schema::table('truckers_messages_attachments', function (Blueprint $table) {
            $table->dropForeign('truckers_messages_attachments_trucker_message_id_foreign');
        });

        Schema::dropIfExists('truckers_messages_attachments');

        Schema::table('truckers_messages', function (Blueprint $table) {
            $table->dropForeign('truckers_messages_trucker_id_foreign');
        });

        Schema::dropIfExists('truckers_messages');
    }
}
