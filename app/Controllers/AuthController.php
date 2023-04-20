<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Controllers\BaseController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{


    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!isset($_COOKIE['COOKIE-SESSION'])) {
            $data['title'] = 'Login';
            return view('login', $data);
        } else {
            return redirect()->to('/dashboard');
        }
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
        $result = $model->login($data);
        if ($result == false) {
            $session->setFlashdata('message', 'Incorrect email or password');
            return redirect()->to('/login');
        } else {
            $key = getenv('JWT_SECRET_KEY');
            $decoded_token = JWT::decode($result, new Key($key, 'HS256'));
            //change it to 1 later
            if ($decoded_token->is_active == 0) {
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('message', 'Email is not activated');
                return redirect()->to('/login');
            }
        }
    }
    public function registerIndex()
    {
        if (!isset($_COOKIE['COOKIE-SESSION'])) {
            $data['title'] = 'Register';
            return view('register', $data);
        } else {
            return redirect()->to('/dashboard');
        }
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
            'avatar' => [
                'rules' => 'uploaded[avatar]|is_image[avatar]|max_size[avatar,500]|mime_in[avatar,image/jpg,image/jpeg,image/png,image/webp]|max_dims[avatar,1000,1000]',
                'errors' => [
                    'uploaded' => 'Please choose an avatar',
                ]
            ],
        ]);

        if (!$validate) {
            $data = [
                'title' => 'Register',
                'validation' => $this->validator
            ];
            return view('register', $data);
        } else {

            $name = $this->request->getVar('name');
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
            $image = $this->request->getFile('avatar');

            $imageName = $image->getRandomName();
            $image->move('assets/img/avatar', $imageName);
            // dd($image);

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $model->encryptPass($password),
                'image' => $imageName,
                'is_active' => 0,
                'date_created' => time(),
            ];
            // dd($data['date_created']);
            if ($model->register($data)) {
                $session->setFlashdata('message-success', 'Account Created !');
                return redirect()->to('/login');
            } else {
                $session->setFlashdata('message', 'This account is already exists!');
                return redirect()->to('/login');
            }
        }
    }

    public function logout()
    {
        $session = \Config\Services::session();
        setcookie('COOKIE-SESSION', null);
        $session->setFlashdata(
            'message-success',
            'Logout Success!'
        );
        return redirect('login');
    }
}
