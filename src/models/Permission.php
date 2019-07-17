<?php

namespace Newtech\SSOAuth\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

use Session;

// use GuzzleHttp\Exception\GuzzleException;
// use GuzzleHttp\Client;

class Permission extends Model
{
    protected $table = 'sso_permissions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name'
    ];

    public static function updateBulk($permissions) {
    	Permission::truncate();
    	foreach ($permissions as $permission) {
    		Permission::create(['name' => $permission['name']]);
    	}
    }
}
