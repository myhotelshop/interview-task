<?php

use Faker\Generator as Faker;
use App\Models\Conversion;

$factory->define(Conversion::class, function (Faker $faker) {
  return [
    'customer_id' => '123',
    'revenue' => $faker->numberBetween(10,100),
    'booking_number' => $faker->randomNumber(),
    'date_of_contact' => $faker->date(),
    'platform' => $faker->randomElement(['trivago','tripadvisor','kiako']),
  ];
});
