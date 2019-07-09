<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablePaymentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
        });

        Schema::table('type_services', function () {
            DB::statement(<<<EOF
INSERT INTO `type_services` (`id`, `description`, `created_at`, `updated_at`) VALUES
(5, 'EXPORT', NULL, NULL);
EOF
            );
        });

        Schema::table('payment_type', function () {
            DB::statement(<<<EOF
INSERT INTO payment_type (id, description, created_at, updated_at) VALUES
    (1, 'CHECK', NULL, NULL), (2, 'TRANSFER', NULL, NULL);
EOF
            );
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('payment_type_id')->unsigned()->nullable()->after('type_service_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_type');
            $table->index(['payment_type_id']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            DB::statement(<<<EOF
ALTER TABLE `tickets` CHANGE `type_service_id` `type_service_id` INT(11) UNSIGNED NOT NULL;
EOF
            );
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('type_service_id')->references('id')->on('type_services');
            $table->index(['type_service_id']);
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
            $table->dropForeign('tickets_payment_type_id_foreign');
            $table->dropForeign('tickets_type_service_id_foreign');
        });

        Schema::dropIfExists('payment_type');
    }
}
