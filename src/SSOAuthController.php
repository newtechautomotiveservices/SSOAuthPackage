<?php

namespace Newtech\SSOAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Newtech\SSOAuth\Models\User;

use Newtech\SSOAuth\Models\Permission;
use Newtech\SSOAuth\Models\Role;

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
        $authenticated = User::authenticate($request['email'], $request['password']);
        if($authenticated["status"] == "success") {
            $checkUser = User::updateUser($authenticated);
            if($checkUser['status'] == "success") {
                $request->session()->put('user_id', $authenticated['rows'][0]['id']);
                $request->session()->put('user_token',$authenticated['rows'][0]['token']);
                return $authenticated;
            } else {
                $request->session()->flush();
                return $authenticated;
            }
        } else {
            return $authenticated;
        }
    }

    public function indexLogout() {
        session()->flush();
        return redirect(config('ssoauth.main.login_route'));
    }


    public function updateProjectPermissions(Request $request) {
        $project_id = $request['rows'][0]['project_id'];
        $permissions = json_decode($request['rows'][0]['permissions']);
        $roles = json_decode($request['rows'][0]['roles']);
        if(is_array($roles)) {
            Role::updateBulk($roles);

        } else {
            return "false";
        }
        if(is_array($permissions)) {
            Role::updateBulk($permissions);
        } else {
            return "false";
        }
    }

    public function indexPanel(Request $request) {
        $config = config('ssoauth.main');
        $users_count = User::all()->count();
        $permissions_count = Permission::all()->count();
        $roles_count = Role::all()->count();
        $role = Role::find(1);
        $last_synced = "Never";
        if($role) {
            $last_synced = Role::find(1)->created_at;
        }
        
        $api_url = $request->root() . "/ssoauth/api/updateProjectPermissions";

        return view('ssoauth::panel')
        ->with('api_url', $api_url)
        ->with('users_count', $users_count)
        ->with('permissions_count', $permissions_count)
        ->with('roles_count', $roles_count)
        ->with('last_synced', $last_synced)
        ->with('config', $config);
    }

    public function updateProjectConfiguration(Request $request) {
        $id = $request['id'] ?: '';
        $login = $request['login_route'] ?: '';
        $logout = $request['logout_route'] ?: '';
        $home = $request['home_route'] ?: '';
        $apiKey = $request['api_key'] ?: '';

        config('project_id', $id);
        config('login_route', $login);
        config('logout_route', $logout);
        config('home_route', $home);
        config('sso_api_key', $apiKey);
        
        return "true";
    }
}
