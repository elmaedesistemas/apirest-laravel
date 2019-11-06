<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
         // check if user is identified
         $token = $request->header('Authorization');
         $jwtAuth = new \JwtAuth();
         $checkToken = $jwtAuth->checkToken($token);

         if($checkToken) {
             return $next($request);

         }else{
             // Message Error
             $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'user not identified successfully'
            );

            return response()->json($data, $data['code']);
         }
        return $next($request);
    }
}
