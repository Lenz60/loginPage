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

function createJWT($id, $email, $is_active)
{
    $requestTime = time();
    $tokenTIme = getenv('JWT_TIME_EXP');
    $tokenExpireTime = $requestTime + $tokenTIme;
    $payload = [
        'id' => $id,
        'email' => $email,
        'is_active' => $is_active,
        'iat' => $requestTime,
        'exp' => $tokenExpireTime
    ];
    $jwt = JWT::encode($payload, getenv('JWT_SECRET_KEY'), 'HS256');
    return $jwt;
}
