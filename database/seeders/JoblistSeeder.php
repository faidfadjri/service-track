<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class JoblistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            DB::table('joblist')->insert([
                'VehicleId' => $index, 
                'WO' => $faker->word,
                'ServiceDate' => $faker->dateTimeBetween('-1 year', 'now'),
                'ServiceEndDate' => $faker->dateTimeBetween('-1 year', 'now'),
                'ReleaseDate' => $faker->dateTimeBetween('-1 year', 'now'),
                'isPaid' => $faker->boolean,
                'UserId' => 1,
                'ProgressId' => 2, 
                'JobTypeId' => $faker->numberBetween(1, 4),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}