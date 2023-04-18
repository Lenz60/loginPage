<?php

namespace App\Models;

use CodeIgniter\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ProfileModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';

    public function show($token)
    {
        $model = new ProfileModel();
        $key = getenv('JWT_SECRET_KEY');
        $decoded_token = JWT::decode($_COOKIE['COOKIE-SESSION'], new Key($key, 'HS256'));
        $builder = $this->table('users');
        $data = $builder->where('id', $decoded_token->id)->first();
        if (!$data) {
            return false;
        } else {
            $data2 = [
                'email' => $data['email'],
                'name' => $data['name'],
                'image' => $data['image'],
            ];
            return $data2;
        }
    }
}
