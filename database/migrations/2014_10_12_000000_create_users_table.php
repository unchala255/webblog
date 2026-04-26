<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $col) {
            $col->id();
            $col->string('username', 50)->unique();
            $col->string('email', 100)->unique();
            $col->string('password', 255);
            $col->string('display_name', 100)->nullable();
            $col->string('avatar', 255)->nullable();
            $col->enum('role', ['admin', 'author', 'user'])->default('user');
            $col->text('bio')->nullable();
            $col->boolean('is_active')->default(true);
            $col->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
