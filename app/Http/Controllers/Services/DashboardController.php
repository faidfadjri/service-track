<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Helper\Data;
use App\Models\Joblist;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $data = Data::masterData('Dashboard');
        $lists = $data->get();

        return view('pages.service.dashboard', [
            'lists' => $lists
        ]);
    }

    public function data () {
        $data = Data::masterData('Dashboard');
        $lists = $data->get();

        return $lists;
    }
}
