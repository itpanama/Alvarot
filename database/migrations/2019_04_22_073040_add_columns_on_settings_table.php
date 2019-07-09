<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsOnSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('email_trucker_list')->nullable()->after('id');
            $table->text('email_counter_list')->nullable()->after('id');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('bbc_mails');
        });

        $sql = <<<EOF
update settings set email_trucker_list = 'lilina.delrio@msc.com\nsebastian.rodriguez@msc.com\nintermodal.panama@msc.com';
EOF;

        Schema::table('settings', function () use ($sql) {
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
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('email_counter_list');
            $table->dropColumn('email_trucker_list');
        });
    }
}
