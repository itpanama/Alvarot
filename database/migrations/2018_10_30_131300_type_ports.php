<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TypePorts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
INSERT INTO `type_ports` (`id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Balboa', NULL, NULL),
(2, 'Cristobal', NULL, NULL),
(3, 'Rodman', NULL, NULL);
EOF;
        Schema::table('type_ports', function () use($sql) {
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
