<?php

Route::group(['middleware' => ['web']], function () {
	Route::get(config('ssoauth.main.login_route'), 'Newtech\SSOAuth\SSOAuthController@indexLogin');
	Route::post('/ssoauth/ajax' . config('ssoauth.main.login_route'), 'Newtech\SSOAuth\SSOAuthController@postLogin');
	Route::post('/ssoauth/ajax/updateProjectConfiguration', 'Newtech\SSOAuth\SSOAuthController@updateProjectConfiguration');
	Route::get(config('ssoauth.main.logout_route'), 'Newtech\SSOAuth\SSOAuthController@indexLogout');
	Route::group(['middleware' => ['ssoauth']], function () {
		Route::get('/ssoauth/panel', 'Newtech\SSOAuth\SSOAuthController@indexPanel');
		Route::get('/test', 'Newtech\SSOAuth\SSOAuthController@indexPanel');
	});
});



Route::prefix('api')->group(function () {
    Route::group(['middleware' => ['api']], function () {
    	Route::post('/ssoauth/pass_session', 'Newtech\SSOAuth\SSOAuthController@passSession');
        Route::post('/ssoauth/api/updateProjectPermissions', 'Newtech\SSOAuth\SSOAuthController@updateProjectPermissions');
    });
});