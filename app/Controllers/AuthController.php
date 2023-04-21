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
        if (!$result) {
            $session->setFlashdata('message', 'Incorrect email or password');
            return redirect()->to('/login');
        } else if ($result == 'no account') {
            $session->setFlashdata('message', 'There is no acount with that email address');
            return redirect()->to('/login');
        } else {
            $key = getenv('JWT_SECRET_KEY');
            $decoded_token = JWT::decode($result, new Key($key, 'HS256'));
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
        $userModel = new UserModel();
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
            ]
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
                'password' => $userModel->encryptPass($password),
                'image' => $imageName,
                'is_active' => 0,
                'date_created' => time(),
            ];
            // dd($data['date_created']);
            $register = $userModel->register($data);
            if ($register) {
                //Save the avatar if the user is registered
                $image->move('assets/img/avatar', $imageName);
                //Generate Activation Token
                $activationToken = $userModel->generateActivationToken();

                //Save the activation token to database 
                $user_token = [
                    'email' => $email,
                    'token' => $activationToken,
                    'type' => 'register',
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

        if ($result == 'register') {
            if ($userModel->activateUser($email)) {
                $session->setFlashdata('message-success', 'The account is activated!');
                $tokenModel->deleteToken($token);
                return redirect()->to('/login');
            }
        } else if ($result == 'forgot') {
            $data = [
                'title' => 'Forgot Password',
                'email' => $email,
                'token' => $token
            ];
            $session->set($data);
            return view('reset', $data);
        } else {
            $session->setFlashdata('message', 'Token invalid');
            return redirect()->to('/login');
        }

        return view('login', $data);
    }

    public function forgotIndex()
    {
        if (!isset($_COOKIE['COOKIE-SESSION'])) {
            $data['title'] = 'Forgot Password';
            return view('forgot', $data);
        } else {
            return redirect()->to('/dashboard');
        }
    }

    public function forgotPassword()
    {
        //Todo : Check the email first 
        //if it exsists, send reset password email
        //if its not exists, throw flash message
        $session = \Config\Services::session();
        $emailActivation = \Config\Services::email();
        $userModel = new UserModel();
        $tokenModel = new TokenModel();
        $email = $this->request->getVar('forgot-email');
        $result = $userModel->forgotPass($email);
        if ($result) {
            //Generate Activation Token
            $activationToken = $userModel->generateActivationToken();

            //Save the activation token to database 
            $forgot_token = [
                'email' => $email,
                'token' => $activationToken,
                'type' => 'forgot',
                'date_created' => time(),
            ];
            if ($tokenModel->saveToken($forgot_token)) {
                $name = $userModel->getName($email);
                if ($name) {
                    //Send Confirmation email with token
                    $emailActivation->setFrom('raflytestproject@gmail.com', 'Rafly Andrian Wicaksana');
                    $emailActivation->setTo($email);
                    $emailActivation->setSubject('Reset Password Confirmation');
                    $emailActivation->setMessage(

                        '<h1>Hello ' . $name . '</h1>
                        <p>Recently you requested for password change, please click link below to reset your password</p>
                        <a href="' . base_url() . 'auth/verify?email=' . $email . '&token=' . urlencode($activationToken) . '">Reset</a>'

                    );
                    if ($emailActivation->send()) {
                        $session->setFlashdata('message-success', 'Check your email for confirmation email.');
                        return redirect()->to('/login');
                    } else {
                        echo $this->email->print_debugger();
                        die;
                    }
                } else {
                }
            } else {
                $session->setFlashdata('message', 'Error saving token to database');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('message', 'There is no account with that email address');
            return redirect()->to('/auth/forgot');
        }
    }

    public function resetPassword()
    {
        $session = \Config\Services::session();
        $userModel = new UserModel();
        $tokenModel = new TokenModel();
        $email = $session->get('email');
        $password = $this->request->getVar('reset-password');
        $token = $session->get('token');
        // dd($password);
        // validate the form
        $validate = $this->validate([
            'reset-password' => [
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => 'Please enter your password'
                ]
            ],
            'reset-confirm-password' => [
                'rules' => 'required|min_length[3]|matches[reset-password]',
                'errors' => [
                    'required' => 'Please enter your password',
                    'matches' => 'The password is not match'
                ]
            ]
        ]);
        if (!$validate) {
            $data = [
                'title' => 'Reset Password',
                'email' => $email,
                'validation' => $this->validator
            ];
            return view('reset', $data);
        } else {
            $data = [
                'email' => $email,
                'password' => $userModel->encryptPass($password)
            ];
            $check = $userModel->checkPass($data['password']);
            if ($check) {
                $data = [
                    'title' => 'Reset Password',
                    'email' => $email,
                ];
                $session->setFlashdata('message', "New password can't be your current password");
                return view('reset', $data);
            } else {
                $result = $userModel->resetPass($data);
                if ($result) {
                    $session->setFlashdata('message-success', 'Password changed!');
                    $tokenModel->deleteToken($token);
                    return redirect()->to('/login');
                } else {
                    $session->setFlashdata('message', 'Password reset failed!');
                    return redirect()->to('/login');
                }
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
