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
        if($request->session()->has('_user_id')) {
            if($request->session()->has('requestCount')) {
                $requestCount = $request->session()->get('requestCount');
                if($requestCount >= config('ssoauth.main.refresh_interval')) {
                    $token = $request->session()->get('_user_token');
                    $user_id = $request->session()->get('_user_id');
                    $verify = User::verify($token, $user_id);
                    $requestCount = 0;
                    $request->session()->put('requestCount', $requestCount);
                    $request->session()->save();
                    if($verify) {
                        return $next($request);
                    } else {
                        $request->session()->flush();
                        return redirect(config('ssoauth.main.login_route'));
                    }
                } else {
                    $requestCount = $requestCount + 1;
                    $request->session()->put('requestCount', $requestCount);
                    $request->session()->save();
                    return $next($request);
                }
            } else {
                $requestCount = 0;
                $request->session()->put('requestCount', $requestCount);
                $request->session()->save();
                return $next($request);
            }

        } else {
            $request->session()->flush();
            return redirect(config('ssoauth.main.login_route'));
        }
    }

    private function checkToken($request) {

    }
}
