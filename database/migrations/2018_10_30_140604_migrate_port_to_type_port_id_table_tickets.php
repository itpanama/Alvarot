<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigratePortToTypePortIdTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "update tickets set type_port_id = (select type_ports.id from type_ports where type_ports.description = tickets.port)";

        Schema::table('tickets', function () use($sql) {
            DB::statement($sql);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('port');
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
            $table->string('port')->nullable()->after('user_id_assigned');
        });
    }
}
