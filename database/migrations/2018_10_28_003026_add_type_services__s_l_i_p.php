<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeServicesSLIP extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
INSERT INTO `type_services` (`id`, `description`, `created_at`, `updated_at`) 
VALUES ('6', 'SLIP', NULL, NULL);
EOF
        ;

        Schema::table('type_services', function () use($sql) {
            DB::statement($sql);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
