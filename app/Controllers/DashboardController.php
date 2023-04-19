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
        $session->set('name', $result['name']);
        return view('dashboard');
    }
}
