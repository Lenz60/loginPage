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
            $session->setFlashdata('message', '<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-300 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">Session Expired, Please login again</span>
              </div>');
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
