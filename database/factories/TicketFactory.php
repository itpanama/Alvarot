<?php

use Faker\Generator as Faker;

$factory->define(App\Ticket::class, function (Faker $faker) {
    $bl_number = array_merge(
        $faker->randomElements($array = range('A', 'Z'), $count = 4),
        $faker->randomElements($array = range(0, 9), $count = 7));

    $customer_id = factory(App\Customer::class)->create()->id;

    $type_port_id = null;

    $type_port_description = $faker->randomElement($array = array('Balboa', 'Cristobal', 'Rodman'));
    $type_port = App\TypePort::where('description', '=', $type_port_description)->first();

    if ($type_port) {
        $type_port_id = $type_port->id;
    }

    return [
        'customer_id' => $customer_id,
        'bl_number' => join('', $bl_number),
        'type_service_id' => function () use ($faker) {
            $id = $faker->numberBetween(1, 6);
            return App\TypeService::find($id)->id;
        },
        'payment_type_id' => function () use ($faker) {
            $id = $faker->numberBetween(1, 2);
            return App\PaymentType::find($id)->id;
        },
        'comments' => $faker->paragraph($nb = 3, $variableNbSentences = true),
        'type_port_id' => $type_port_id,
    ];
});
