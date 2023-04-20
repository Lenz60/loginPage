<?php

namespace App\Models;

use CodeIgniter\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DashboardModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';

    public function show($token)
    {
        $model = new DashboardModel();
        $key = getenv('JWT_SECRET_KEY');
        $decoded_token = JWT::decode($_COOKIE['COOKIE-SESSION'], new Key($key, 'HS256'));
        $builder = $this->table('users');
        $data = $builder->where('email', $decoded_token->email)->first();
        if (!$data) {
            return false;
        } else {
            $data2 = [
                'email' => $data['email'],
                'name' => $data['name'],
                'image' => $data['image'],
                'date_created' => $data['date_created']
            ];
            return $data2;
        }
    }
}
