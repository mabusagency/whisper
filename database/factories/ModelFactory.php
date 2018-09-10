<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'type' => 'admin',
    ];
});


$factory->define(App\Student::class, function (Faker\Generator $faker) {

    $titles = ['owner','founder','manager','president'];

    $firstName = $faker->firstName;
    $lastName = $faker->lastName;

    return [
        'institution_id' => 1,
        'campaign_id' => rand(1,2),
        'purl1' => $firstName,
        'purl2' => $lastName,
        'firstName' => $firstName,
        'lastName' => $lastName,
        'company' => $faker->company,
        'title' => $titles[rand(0,3)],
        'email' => $faker->email,
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->stateAbbr,
        'zip' => $faker->postcode,
        'status' => 0,
    ];
});
