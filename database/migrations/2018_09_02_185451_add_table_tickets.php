<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('email_optional')->nullable();
            $table->integer('user_id')->unsigned();
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->index(['user_id']);

            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->unsigned();
            $table->string('bl_number', 12);
            $table->integer('type_service_id');
            $table->text('comments')->nullable();
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->index(['customer_id']);

            $table->timestamps();
        });

        Schema::create('tickets_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachment_name');
            $table->integer('attachment_size');
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
        Schema::dropIfExists('type_services');

        // Schema::table('customers', function (Blueprint $table) {
        //     $table->dropForeign('customers_user_id_foreign');
        // });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign('tickets_customer_id_foreign');
        });

        Schema::table('tickets_attachments', function (Blueprint $table) {
            $table->dropForeign('tickets_attachments_ticket_id_foreign');
        });
        
        Schema::dropIfExists('customers');
        
        // Schema::table('tickets', function (Blueprint $table) {
        //     $table->dropForeign('tickets_customer_id_foreign');
        // });

        Schema::dropIfExists('tickets');

        // Schema::table('tickets_attachments', function (Blueprint $table) {
        //     $table->dropForeign('tickets_attachments_ticket_id_foreign');
        // });
        
        Schema::dropIfExists('tickets_attachments');
    }
}
