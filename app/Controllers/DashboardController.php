<?php

namespace App\Controllers;

use App\Models\DashboardModel;
use App\Controllers\BaseController;


class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->model = new DashboardModel();
    }
    public function index()
    {
        //
        return view('dashboard');
    }

    public function show()
    {
        $session = \Config\Services::session();
        $token = $_COOKIE['COOKIE-SESSION'];
        $model = new DashboardModel();
        $result = $model->show($token);
        $data = [
            'email' => $result['email'],
            'name' => $result['name'],
            'image' => $result['image'],
            'date_created' => $result['date_created']
        ];
        return view('dashboard', $data);
    }
}
