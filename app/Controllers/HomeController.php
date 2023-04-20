<?php

namespace App\Controllers;


use App\Models\ProfileModel;
use App\Controllers\BaseController;


class HomeController extends BaseController
{
    public function __construct()
    {
    }
    public function index()
    {
        // return view('home');
        // 
        // $decoded_token = JWT::decode($_COOKIE['COOKIE-SESSION'], new Key($key, 'HS256'));

        return view('home');
    }

    // public function show()
    // {
    //     $token = $_COOKIE['COOKIE-SESSION'];
    //     $model = new ProfileModel();
    //     $result = $model->show($token);
    //     print_r($result);
    // }
}
