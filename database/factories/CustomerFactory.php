<?php

use Faker\Generator as Faker;

$factory->define(App\Customer::class, function (Faker $faker) {
    $user = new App\User();
    $user->name = $faker->name;
    $user->username = $faker->userName;
    $user->email = $faker->unique()->safeEmail;
    
    // role 3: customer
    $user->role_id = 3; 
    $user->password = Hash::make('123123');
    $user->save();

    return [
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'email_optional' => $faker->unique()->safeEmail,
    ];
});