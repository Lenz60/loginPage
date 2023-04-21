<?php

namespace App\Models;

use CodeIgniter\Model;

class TokenModel extends Model
{
    protected $table            = 'user_token';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['email', 'token', 'type', 'date_created'];

    public function saveToken($dataInserted)
    {
        $model = new TokenModel();
        $email = $dataInserted['email'];
        $builder = $this->table('user_token');
        $data = $builder->where('email', $email)->first();
        if (!$data) {
            $model->save($dataInserted);
            return true;
        } else {
            $builder->where('email', $dataInserted['email']);
            $builder->delete();
            $builder->save($dataInserted);
            return true;
        }
    }

    public function checkToken($dataInserted)
    {
        $model = new TokenModel();
        $email = $dataInserted['email'];
        $token = $dataInserted['token'];
        $builder = $this->table('user_token');
        $data = $builder->where('email', $email)->first();
        if (!$data) {
            return false;
        } else {
            if ($data['token'] != $token) {
                return false;
            } else {
                return $data['type'];
            }
        }
    }

    public function deleteToken($token)
    {
        $model = new TokenModel();
        $builder = $this->table('user_token');
        $data = $builder->where('token', $token);
        if (!$data) {
            return false;
        } else {
            $builder->delete();
            return true;
        }
    }
}
