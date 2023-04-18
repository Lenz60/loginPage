<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;

class AuthController extends BaseController
{


    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['title'] = 'Login';
        return view('login', $data);
    }

    public function loginIndex()
    {
        return view('login');
    }

    public function login()
    {
        $session = \Config\Services::session();
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $data = [
            'email' => $email,
            'password' => $model->encryptPass($password)
        ];
        if ($model->login($data)) {
            return view('home');
        } else {
            $session->setFlashdata('message', '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Email or Password is wrong!</span>
              </div>');
            return redirect()->to('/auth');
        }
    }

    public function registerindex()
    {
        return view('register');
    }

    public function register()
    {
        $session = \Config\Services::session();
        $model = new UserModel();
        $validate = $this->validate([
            'name' => [
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => 'Please enter your name'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Please enter your email'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Please enter your password'
                ]
            ],
            'confirm-password' => [
                'rules' => 'required|min_length[3]|matches[password]',
                'errors' => [
                    'required' => 'Please enter your password',
                    'matches' => 'The password is not match'
                ]
            ],
        ]);

        if (!$validate) {
            return view('register', ['validation' => $this->validator]);
        } else {

            $name = $this->request->getVar('name');
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $image = 'default.jpg';

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $model->encryptPass($password),
                'image' => $image,
                'is_active' => 0,
                'date_created' => time()
            ];
            if ($model->register($data)) {
                $session->setFlashdata('message', '<div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Account Created !</span> Please login.
          </div>');
                return redirect()->to('/auth');
            } else {
                $session->setFlashdata('message', '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">This account is already exists!</span>
              </div>');
                return redirect()->to('/auth');
            }
        }
    }
}
