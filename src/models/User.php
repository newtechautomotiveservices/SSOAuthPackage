<?php

namespace Newtech\SSOAuth\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Session;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

class User extends Model
{
    protected $table = 'sso_users';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'roles',
        'permissions'
    ];

    public static function user() {
        $user_id = Session::get("user_id");
        $user = User::where('user_id', '=', $user_id)->first();
        return($user);
    }

    public static function authenticate($email, $password) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => config('ssoauth.main.sso_api_url') . config('ssoauth.api.user/login'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n" . $email . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n" . $password . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
          CURLOPT_HTTPHEADER => array(
            "Postman-Token: ea1c5e83-e23e-463a-9832-7c9d86ca3b82",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
          ),
        ));

        $output = curl_exec($curl);
        $output = json_decode($output, true);
        if($output["status"] != "failure") {
            return $output;
        } else {
            return "false";
        }
    }

    public static function updateUser($authenticated) {
        // dd($authenticated);
        $user = User::where('user_id', '=', $authenticated['rows'][0]['id'])->first();
        if($user) {
            $user->user_id = $authenticated['rows'][0]['id'];
            $user->store_number = $authenticated['rows'][0]['store_number'];
            $user->first_name = $authenticated['rows'][0]['first_name'];
            $user->last_name = $authenticated['rows'][0]['last_name'];
            $user->email = $authenticated['rows'][0]['email'];
            $user->roles = json_encode($authenticated['rows'][0]['roles']);
            $user->permissions = json_encode($authenticated['rows'][0]['permissions']);
            $user->save();

            $sessionData = [
                'user_id' => $authenticated['rows'][0]['id'],
                'user_token' => $authenticated['rows'][0]['token']
            ];
            session()->put('user_id', $sessionData["user_id"]);
            session()->put('user_token', $sessionData["user_token"]);
            return $sessionData;
        } else {
            User::newUser($authenticated);
            $sessionData = [
                'user_id' => $authenticated['rows'][0]['id'],
                'user_token' => $authenticated['rows'][0]['token']
            ];
            return $sessionData;
        }
    }

    public static function newUser($authenticated) {
        $user = new User();
        $user->user_id = $authenticated['rows'][0]['id'];
        $user->store_number = $authenticated['rows'][0]['store_number'];
        $user->first_name = $authenticated['rows'][0]['first_name'];
        $user->last_name = $authenticated['rows'][0]['last_name'];
        $user->email = $authenticated['rows'][0]['email'];
        $user->roles = json_encode($authenticated['rows'][0]['roles']);
        $user->permissions = json_encode($authenticated['rows'][0]['permissions']);

        $user->save();
    }

    public function verify () {
        $token = $this->token;
        $user_id = $this->user_id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_PORT => "8000",
          CURLOPT_URL => config('ssoauth.main.sso_api_url') . config('ssoauth.api.user/verify'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"user_id\"\r\n\r\n" . $user_id . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n" . $token . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"project_id\"\r\n\r\n" . config('ssoauth.main.project_id') . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
          CURLOPT_HTTPHEADER => array(
            "Postman-Token: ff373564-5a62-493a-a247-35506dd489d2",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
          ),
        ));

        $output = json_decode(curl_exec($curl), true);
        if($output["status"] === "success") {
            return "true";
        } else {
            return "false";
        }

    }
}
