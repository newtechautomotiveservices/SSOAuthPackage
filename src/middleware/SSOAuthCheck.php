<?php

namespace Newtech\SSOAuth\Middleware;

use Closure;

use Newtech\SSOAuth\Models\User;

class SSOAuthCheck
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


        if($request->session()->has('user_id')) {
            $token = $request->session()->get('user_token');
            $user_id = $request->session()->get('user_id');
            $verify = User::verify($token, $user_id);
            if($verify['status'] == "success") {
                return $next($request);
            } else {
                $request->session()->flush();
                return redirect(config('ssoauth.main.login_route'));
            }
        } else {
            $request->session()->flush();
            return redirect(config('ssoauth.main.login_route'));
        }


    }
}
