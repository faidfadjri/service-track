<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(public_path('docs/jobtype.json'));
        $arrayData = json_decode($jsonString, true);

        foreach($arrayData as $jobtype){

            $data = [
                'name'       => $jobtype['type'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            DB::table('master_job')->insert($data);
        }
    }
}
