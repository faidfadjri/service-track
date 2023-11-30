<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(public_path('docs/roles.json'));
        $arrayData = json_decode($jsonString, true);

        $division = ['General Repair'];

        foreach ($arrayData as $role) {

            $data = [
                'name'      => $role['role'],
                'division'  => $division[0]
            ];

            DB::table('master_roles')->insert($data);
        }
    }
}
