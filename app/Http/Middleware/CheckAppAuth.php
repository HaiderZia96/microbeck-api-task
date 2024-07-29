<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppAuth
{
    use \App\Traits\Api\Response;
    public $data;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $app_token = $request->header('token');
        $auth_pass = \Config::get('services.authPass.auth_pass');
        if($app_token != $auth_pass){
            $this->data=['status_code'=>401,
                "error"=>["token is incorrect"],
                'data'=>[]
            ];
            $this->setResponse($this->data);
            return $this->getResponse();
        }
        return $next($request);
    }
}
