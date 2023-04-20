<?php

namespace App\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\UserModel;
use App\Models\TokenModel;
use App\Controllers\BaseController;

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
            if ($decoded_token->is_active == 1) {
                $expireCookie = time() + 3600000;
                setcookie("COOKIE-SESSION", $result, $expireCookie, '/', null, 'null', true);
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
        $emailActivation = \Config\Services::email();
        $model = new UserModel();
        $tokenModel = new TokenModel();
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
            $register = $model->register($data);
            if ($register) {
                //Save the avatar if the user is registered
                $image->move('assets/img/avatar', $imageName);
                //Generate Activation Token
                $activationToken = $model->generateActivationToken();

                //Save the activation token to database 
                $user_token = [
                    'email' => $email,
                    'token' => $activationToken,
                    'date_created' => time(),
                ];
                if ($tokenModel->saveToken($user_token)) {
                    //Send Confirmation email with token
                    $emailActivation->setFrom('raflytestproject@gmail.com', 'Rafly Andrian Wicaksana');
                    $emailActivation->setTo($email);
                    $emailActivation->setSubject('Confirmation Email');
                    $emailActivation->setMessage(

                        '<h1>Hello ' . $name . '</h1>
                        <p>Recently you registered to our site, to activate your account, please click link below</p>
                        <a href="' . base_url() . 'auth/verify?email=' . $email . '&token=' . urlencode($activationToken) . '">Activate</a>'

                    );
                    if ($emailActivation->send()) {
                        $session->setFlashdata('message-success', 'Account Created !, Check your email for confirmation email.');
                        return redirect()->to('/login');
                    } else {
                        echo $this->email->print_debugger();
                        die;
                    }
                } else {
                    $session->setFlashdata('message', 'Error saving token to database');
                    return redirect()->to('/login');
                }
            } else {
                $session->setFlashdata('message', 'This account is already exists!');
                return redirect()->to('/login');
            }
        }
    }

    public function verify()
    {
        //change later
        $session = \Config\Services::session();
        $data['title'] = 'Verify Account';
        $tokenModel = new TokenModel();
        $userModel = new UserModel();
        $email = $this->request->getVar('email');
        $token = urldecode($this->request->getVar('token'));
        $data = [
            'email' => $email,
            'token' => $token
        ];
        $result = $tokenModel->checkToken($data);
        // dd($token);

        if ($result) {
            if ($userModel->activateUser($email)) {
                $session->setFlashdata('message-success', 'The account is activated!');
                $tokenModel->deleteToken($token);
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('message', 'Token Invalid');
            return redirect()->to('/login');
        }

        return view('login', $data);
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
