<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = \Config\Services::session();
        if (!isset($_COOKIE['COOKIE-SESSION'])) {
            $session->setFlashdata('message', 'Session Expired, Please login again');
            return redirect()->to('/login');
        } else {
            try {
                helper('jwt');
                $encodedToken = $_COOKIE['COOKIE-SESSION'];
                validateJWT($encodedToken);
                return $request;
            } catch (Exception $e) {
                return Services::response()->setJson([
                    'error' => $e->getMessage()
                ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
