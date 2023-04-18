<?php

namespace App\Models;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['name', 'email', 'password', 'image', 'created_at'];

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
            $data = $builder->where('password', $password)->first();
            if (!$data) {
                return false;
            } else {
                return true;
            }
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
            // //can be changed to return something and check in controller
            // //then pass it to alert in home
            // throw new PageNotFoundException('Account already exists.');
            return false;
        }
    }
}
