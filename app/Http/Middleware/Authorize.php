<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token  = $request->session()->get('token');
        if (!$token) {
            return redirect('/login');
        }

        try {
            $user = JWTAuth::setToken($token)->toUser();
        } catch (TokenExpiredException $e) {
            return redirect('/login')->with('error', 'Token has expired')->setStatusCode(401);
        } catch (TokenInvalidException $e) {
            return redirect('/login')->with('error', 'Token is invalid')->setStatusCode(401);
        } catch (JWTException $e) {
            return redirect('/login')->with('error', 'Token is invalid')->setStatusCode(401);
        }

        return $next($request);
    }
}
