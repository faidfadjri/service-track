<?php

namespace App\Http\Helper;

use App\Models\Joblist;
use App\Models\Jobtype;
use Illuminate\Database\Eloquent\Model;

class Update
{
    public static function data($modelName, $array)
    {
        $model = app("App\\Models\\$modelName");

        $data = $model::find($array['id']);
        $data->update($array);

        return "$modelName updated successfully";
    }
}