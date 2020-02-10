<?php

use Faker\Generator as Faker;
use App\Models\Customer;

$factory->define(Customer::class, function (Faker $faker) {
  return [
    'id' => '123',
    'name' => 'Ahmed'
  ];
});
