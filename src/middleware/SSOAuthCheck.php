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


        if($request->session()->has('user_id') && $request->session()->has('user_token')) {
            $user = User::user();
            $verify = $user->verify();
            if($verify == "true") {
                return $next($request);
            } else {
                $request->session()->flush();
                return redirect(config('cssoauth.main.login_route'));
            }
        } else {
            $request->session()->flush();
            return redirect(config('cssoauth.main.login_route'));
        }


    }
}
