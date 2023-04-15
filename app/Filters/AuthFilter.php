<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\JWTCI4;
use Config\Services;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!$request->header('Authorization')) {
            $response = Response();
            $response->setJSON([
                'status' => 401,
                'messages' => ['error' => 'Unauthorized. Token is required!']
            ]);
            $response->setStatusCode(401);
            return $response;
        }

        $token = $request->header('Authorization');
        $jwt = new JWTCI4;
        $verify = $jwt->parse($token);

        $response = Response();

        if (!$verify['success']) {
            $response->setJSON([
                'status' => 401,
                'messages' => ['error' => $verify['message']]
            ]);
            $response->setStatusCode(401);
            return $response;
        }

        if (!Services::verifyUser(
            $verify['token']->user_id,
            $verify['token']->username,
            $verify['token']->email
        )) {
            $response->setJSON([
                'status' => 401,
                'messages' => ['error' => 'Unauthorized user. Please re-login']
            ]);
            $response->setStatusCode(401);
            return $response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
