<?php

Route::group(['middleware' => ['web']], function () {
	Route::get(config('ssoauth.main.login_route'), 'Newtech\SSOAuth\SSOAuthController@indexLogin');
	Route::post('/a' . config('ssoauth.main.login_route'), 'Newtech\SSOAuth\SSOAuthController@postLogin');
	Route::get(config('ssoauth.main.logout_route'), 'Newtech\SSOAuth\SSOAuthController@indexLogout');
});
