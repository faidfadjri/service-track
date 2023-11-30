<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $joblistIds = DB::table('joblist')->pluck('id')->toArray();
        $progressIds = DB::table('master_progress')->pluck('id')->toArray();

        foreach (range(1, 10) as $index) {
            DB::table('history')->insert([
                'JobId' => $index,
                'ProgressId' => 1,
                'CLockOnAt' => now(),
                'ClockOffAt' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (range(1, 10) as $index) {
            DB::table('history')->insert([
                'JobId' => $index,
                'ProgressId' => 2,
                'CLockOnAt' => now(),
                'ClockOffAt' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}