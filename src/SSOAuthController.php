<?php

namespace Newtech\SSOAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Newtech\SSOAuth\Models\User;

class SSOAuthController extends Controller
{
    public function indexLogin() {

        if(session()->has('user_id') && session()->has('user_token')) {
            return redirect('/');
        } else {
            return view('ssoauth::login');
        }

    }

    public function postLogin(Request $request) {
        $authenticated = User::authenticate($request['username'], $request['password']);
        if(is_array($authenticated)) {
            $checkUser = User::updateUser($authenticated);
            if(is_array($checkUser)) {
                $request->session()->put('user_id', $checkUser['user_id']);
                $request->session()->put('user_token',$checkUser['user_token']);
                return 'true';
            } else {
                $request->session()->flush();
                return 'false';
            }
        } else {
            return ucfirst($authenticated['metrics']['error']) . ' is incorrect.';
        }
    }

    public function indexLogout() {
        session()->flush();
        return redirect(config('ssoauth.main.login_route'));
    }
}
