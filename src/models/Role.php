<?php

namespace Newtech\SSOAuth\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Session;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

class Role extends Model
{
    protected $table = 'sso_roles';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
    ];

    public static function updateBulk($roles) {
    	Role::truncate();
    	foreach ($roles as $role) {
    		Role::create(['name' => $role['name']]);
    	}
    }
}
