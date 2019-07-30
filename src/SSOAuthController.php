<?php

namespace Newtech\SSOAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Newtech\SSOAuth\Models\User;

class SSOAuthController extends Controller
{
    public function indexLogin() {
        if(session()->has('_user_id') && session()->has('_user_token')) {
            return redirect('/');
        } else {
            return view('ssoauth::login');
        }
    }

    public function indexLogout() {
        session()->flush();
        return redirect(config('ssoauth.main.login_route'));
    }

    public function postLogin(Request $request) {
        $authenticated = User::authenticate($request['email'], $request['password']);
        return $authenticated;
    }

    public function indexPanel(Request $request) {
        
    }
}
