<?php
return [
		'sso_api_url' => '', // The api url for SSO.
        'login_route' => '', // Route where you want the login (EX :: "/login")
        'logout_route' => '', // Route where you want the logout (EX :: "/logout")
        'home_route' => '/', // Route to your home page.
        'product_id' => '', // You can grab this from the SSO website.
        'refresh_interval' => 1 // How often the pages check authorization (per request)
];
