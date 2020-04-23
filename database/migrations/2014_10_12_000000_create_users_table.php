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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->boolean('is_ban')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->rememberToken();
            $table->softDeletes();
            /*===== relationships =====*/
            $table->unsignedBigInteger('role_id');
            /*===== restrict =====*/
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
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
