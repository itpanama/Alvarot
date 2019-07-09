<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewsletterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newsletter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('attachment_name');
            $table->boolean('active')->default(1);
            $table->integer('created_by_user_id')->unsigned()->nullable();
            $table->foreign('created_by_user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
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
        Schema::table('newsletter', function (Blueprint $table) {
            $table->dropForeign('newsletter_created_by_user_id_foreign');
        });

        Schema::dropIfExists('newsletter');
    }
}
