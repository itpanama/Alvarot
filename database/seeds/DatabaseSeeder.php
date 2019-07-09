<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['id' => 1, 'description' => 'Admin'],
            ['id' => 2, 'description' => 'Employee'],
            ['id' => 3, 'description' => 'Customer'],
//            ['id' => 4, 'description' => 'Trucker'],
        ]);

        DB::table('users')->insert([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'role_id' => 1,
            'password' => Hash::make('admin'),
        ]);

        DB::table('users')->insert([
            'name' => 'employee',
            'username' => 'employee',
            'email' => 'employee@employee.com',
            'role_id' => 2,
            'password' => Hash::make('employee'),
        ]);

        $customer_id = DB::table('users')->insertGetId([
            'id' => 3,
            'name' => 'customer',
            'username' => 'customer',
            'email' => 'levieraf+customer@gmail.com',
            'role_id' => 3,
            'password' => Hash::make('customer'),
        ]);

        DB::table('customers')->insert([
            'user_id' => $customer_id,
            'name' => 'customer',
            'email' => 'levieraf+customer@gmail.com',
            'email_optional' => null,
        ]);

        DB::table('type_services')->insert([
            ['id' => 1, 'description' => 'RELEASE'],
            ['id' => 2, 'description' => 'DEMURRAGE'],
            ['id' => 3, 'description' => 'DETENTION'],
            ['id' => 4, 'description' => 'EMPTY RETURN'],
        ]);

        $this->call(StatusTableSeeder::class);
        $this->call(TicketsTableSeeder::class);
    }
}
