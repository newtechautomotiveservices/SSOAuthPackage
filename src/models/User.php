<?php

namespace Newtech\SSOAuth\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Session;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

class User extends Model
{
    protected $table = 'crm_users';

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

    public function authenticate($username, $password) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => config('ssoauth.main.sso_api_url') . config('ssoauth.main.api.user/login'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n" . $username . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"password\"\r\n\r\n" . $password . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
          CURLOPT_HTTPHEADER => array(
            "Postman-Token: ea1c5e83-e23e-463a-9832-7c9d86ca3b82",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
          ),
        ));

        $output = curl_exec($curl);
        if($output["status"] === "success") {
            curl_close($ch);
            $data = json_decode($output, true);
            return $data;
        } else {
            return "false";
        }
    }

    public function updateUser($authenticated) {
        $user = User::where('user_id', '=', $authenticated['user']['id'])->first();
        if($user) {
            $user->user_id = $authenticated['rows']['id'];
            $user->store_number = $authenticated['rows']['store_number'];
            $user->first_name = $authenticated['rows']['first_name'];
            $user->last_name = $authenticated['rows']['last_name'];
            $user->email = $authenticated['rows']['email'];
            $user->roles = $authenticated['rows']['roles'];
            $user->permissions = $authenticated['rows']['permissions'];
            $user->save();

            $sessionData = [
                'user_id' => $authenticated['rows']['id'],
                'user_token' => $authenticated['rows']['token']
            ];
            session()->put('user_id', $sessionData["user_id"]);
            session()->put('user_token', $sessionData["user_token"]);
            return $sessionData;
        } else {
            $this->newUser($authenticated);
            $sessionData = [
                'user_id' => $authenticated['rows']['id'],
                'user_token' => $authenticated['rows']['token']
            ];
            return $sessionData;
        }
    }

    public function newUser($authenticated) {
        $user = new User();
        $user->user_id = $authenticated['rows']['id'];
        $user->store_number = $authenticated['rows']['store_number'];
        $user->first_name = $authenticated['rows']['first_name'];
        $user->last_name = $authenticated['rows']['last_name'];
        $user->email = $authenticated['rows']['email'];
        $user->roles = $authenticated['rows']['roles'];
        $user->permissions = $authenticated['rows']['permissions'];
        $user->save();
    }

    public function verify () {
        $token = $this->token;
        $user_id = $this->user_id;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_PORT => "8000",
          CURLOPT_URL => config('ssoauth.main.sso_api_url') . config('ssoauth.main.api.user/verify'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"user_id\"\r\n\r\n" . $user_id ."\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"token\"\r\n\r\n" . $token . "\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
          CURLOPT_HTTPHEADER => array(
            "Postman-Token: 1d2ec70d-4be4-4c5b-99db-04357b9bf597",
            "cache-control: no-cache",
            "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
          ),
        ));

        $output = curl_exec($curl);
        if($output["status"] === "success") {
            curl_close($ch);
            return "true";
        } else {
            return "false";
        }

    }
}
