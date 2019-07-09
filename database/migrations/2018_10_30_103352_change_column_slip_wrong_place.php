<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnSlipWrongPlace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
INSERT INTO payment_type (id, description, created_at, updated_at) 
VALUES (3, 'SLIP', NULL, NULL);
EOF
        ;

        Schema::table('payment_type', function () use($sql) {
            DB::statement($sql);
        });

        $sql = <<<EOF
update tickets set type_service_id = 5 where type_service_id = 6;
EOF
        ;

        Schema::table('tickets', function () use($sql) {
            DB::statement($sql);
        });

        $sql = <<<EOF
delete from type_services where id = 6;
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
