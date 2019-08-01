<?php

namespace Newtech\SSOAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Newtech\SSOAuth\Models\User;

use Illuminate\Support\Facades\URL;

class SSOAuthController extends Controller
{
    public function indexLogin() {
      $curl = curl_init();
      $session_url = "";
      if(config('app.debug')) {
        $session_url = "debug^:" . route('api.passSession');
      } else {
        $session_url = route('api.passSession');
      }
      curl_setopt_array($curl, array(
        CURLOPT_URL => config("ssoauth.main.sso_url") . "/api/ssoauth/authenticate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"session_url\"\r\n\r\n" . $session_url . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"product_id\"\r\n\r\n" . config('ssoauth.main.product_id') . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer a34e5206-0d5b-4250-a901-ddea650dcd0c",
          "Postman-Token: b54ece01-19e6-4ac7-8a4d-62fccc0aa245",
          "cache-control: no-cache",
          "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
        ),
      )); 

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);
      return redirect($response);
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
        $user = User::find($request["user_id"]);
        $user->remote_token = $request["user_token"];
        $user->save();
        session()->put('_user_id', $request["user_id"]);
        session()->put('_user_token', $request["user_token"]);
        return redirect(config('ssoauth.main.home_route'));
    }

    public function passSessionDev ($json) {
      $data = json_decode(base64_decode($json), true);
      // dd($data);
        $user = User::find($data["id"]);
        if($user) {
          $user->remote_token = $data['remote_token'];
          $user->save();
          session()->put('_user_id', $user->id);
          session()->put('_user_token', $user->remote_token);
          return redirect(config('ssoauth.main.home_route'));
        } else {
          $userTMP = User::create($data);
          session()->put('_user_id', $userTMP->id);
          session()->put('_user_token', $userTMP->remote_token);
          return redirect(config('ssoauth.main.home_route'));
        }
    }
}
