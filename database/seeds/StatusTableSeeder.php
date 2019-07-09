<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('status')->truncate();
        DB::table('status')->insert([
                                        [ 'id' => 1, 'description' => 'GREEN', 'color' => '#40d813', 'start' => '00:00', 'end' => '01:00', 'system' => 1, 'active' => 1 ],
                                        [ 'id' => 2, 'description' => 'RED', 'color' => '#801616', 'start' => '01:00', 'end' => '02:00', 'system' => 1, 'active' => 1 ],
                                        [ 'id' => 3, 'description' => 'YELLOW', 'color' => '#D5B60C', 'start' => '02:00', 'end' => null, 'system' => 1, 'active' => 1 ],
                                    ]);
    }
}
