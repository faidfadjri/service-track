<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(public_path('docs/users.json'));
        $arrayData = json_decode($jsonString, true);

        foreach ($arrayData as $user) {

            $data = [
                // 'id'         => Str::uuid(),
                'username'   => $user['username'],
                'password'   => Hash::make('AKA@123'),
                'fullname'   => $user['fullname'],
                'roleId'     => $user['role'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            DB::table('users')->insert($data);
        }
    }
}
