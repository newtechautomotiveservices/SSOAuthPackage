<?php

namespace Newtech\SSOAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Newtech\SSOAuth\Models\User;

use Illuminate\Support\Facades\URL;

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

    public function passSessionPost(Request $request) {
        return URL::temporarySignedRoute(
            'signed.pass_session', now()->addMinutes(30), ['user_id' => $request["user_id"], 'user_token' => $request["user_token"]]
        );
    }

    public function passSession (Request $request) {
        session()->put('_user_id', $request["user_id"]);
        session()->put('_user_token', $request["user_token"]);
        return redirect(config('ssoauth.main.home_route'));
    }
}
