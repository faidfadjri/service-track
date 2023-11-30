<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $jsonString = file_get_contents(public_path('docs/model.json'));
        $arrayData = json_decode($jsonString, true);

        foreach($arrayData as $model){

            $data = [
                'name'       => $model['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            DB::table('master_model')->insert($data);
        }
    }
}
