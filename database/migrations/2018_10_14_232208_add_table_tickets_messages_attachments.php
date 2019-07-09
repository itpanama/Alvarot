<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTicketsMessagesAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets_messages_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachment_name');
            $table->integer('attachment_size');
            $table->integer('ticket_message_id')->unsigned();
            $table->foreign('ticket_message_id')->references('id')->on('tickets_messages')->onDelete('cascade');
            $table->index(['ticket_message_id']);

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
        Schema::table('tickets_messages_attachments', function (Blueprint $table) {
            $table->dropForeign('tickets_messages_attachments_ticket_message_id_foreign');
        });

        Schema::dropIfExists('tickets_messages_attachments');
    }
}
