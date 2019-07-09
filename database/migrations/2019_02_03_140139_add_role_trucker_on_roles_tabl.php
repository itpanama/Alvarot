<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleTruckerOnRolesTabl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
INSERT INTO `roles` (id, description) VALUES
(4, 'Trucker');
EOF;
        Schema::table('roles', function () use($sql) {
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
        $sql = <<<EOF
delete from roles where id = 4;
EOF;
        Schema::table('roles', function () use($sql) {
            DB::statement($sql);
        });
    }
}
