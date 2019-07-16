<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SSOUsersTable extends Migration
{
    public function up()
    {
        Schema::create('sso_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('store_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('roles');
            $table->string('permissions');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sso_users');
    }
}
