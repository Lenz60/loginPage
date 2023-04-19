<?php

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWT($header)
{
    if (is_null($header)) {
        throw new Expception("JWT Authentication is failed");
    }
    return explode("", $header)[1];
}

function createJWT($email, $is_active)
{
    $requestTime = time();
    $tokenTIme = getenv('JWT_TIME_EXP');
    $tokenExpireTime = $requestTime + $tokenTIme;
    $payload = [
        'email' => $email,
        'is_active' => $is_active,
        'iat' => $requestTime,
        'exp' => $tokenExpireTime
    ];
    $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
    return $jwt;
}

function validateJWT($token)
{
    $session = \Config\Services::session();
    $model = new UserModel();
    $key = getenv('JWT_SECRET_KEY');
    try {
        $decoded_token = JWT::decode($token, new Key($key, 'HS256'));
    } catch (Exception $e) {
        setcookie('COOKIE-SESSION', null);
        $session->setFlashdata('message', '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-300 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Token Invalid</span>, Please login again
              </div>');
        // echo $e->getMessage();
        return redirect()->to('/auth');
    }
}
