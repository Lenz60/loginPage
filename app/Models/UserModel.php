<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'email', 'password', 'image', 'date_created', 'is_active'];

    public function encryptPass($password)
    {
        $salt1 = getenv('SALT1');
        $salt2 = getenv('SALT2');
        $key = getenv('KEY');
        $saltedPassword = $salt1 . $password . $salt2;
        $encryptedPassword = hash_hmac('sha256', $saltedPassword, $key);
        return $encryptedPassword;
    }

    public function login($dataInserted)
    {
        $model = new UserModel();
        $email = $dataInserted['email'];
        $password = $dataInserted['password'];
        $builder = $this->table('users');
        $data = $builder->where('email', $email)->first();
        if (!$data) {
            return false;
        } else {
            $id = $data['id'];
            $pass = $data['password'];
            $is_active = $data['is_active'];
            if ($pass !== $password) {
                return false;
            } else {
                // dd($email);
                helper('jwt');
                $token = createJWT($email, $is_active);
                return $token;
            }
            // return $data;
        }
    }
    public function register($dataInserted)
    {
        $model = new UserModel();
        $email = $dataInserted['email'];
        $builder = $this->table('users');
        $data = $builder->where('email', $email)->first();
        if (!$data) {
            $model->save($dataInserted);
            return true;
        } else {
            return false;
        }
    }

    public function generateActivationToken()
    {
        $salt = getenv('SALT1');
        $token = base64_encode($salt . random_bytes(32));
        return $token;
    }

    public function activateUser($email)
    {
        $model = new UserModel();
        $builder = $this->table('users');
        $builder->set('is_active', 1);
        $builder->where('email', $email);
        $result = $builder->update();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
