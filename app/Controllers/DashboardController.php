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
        $session = \Config\Services::session();
        if (!isset($_COOKIE['COOKIE-SESSION'])) {
            $data['title'] = 'Login';
            $session->setFlashdata('message', 'Session expired, Please login again');
            return view('login', $data);
        } else {
            $this->show();
            return view('dashboard');
        }
    }

    public function show()
    {
        $session = \Config\Services::session();
        $token = $_COOKIE['COOKIE-SESSION'];
        $model = new DashboardModel();
        $result = $model->show($token);
        $data = [
            'title' => 'Dashboard',
            'email' => $result['email'],
            'name' => $result['name'],
            'image' => $result['image'],
            'date_created' => $result['date_created']
        ];
        $session->set('title', $data['title']);
        return view('dashboard', $data);
    }
}
