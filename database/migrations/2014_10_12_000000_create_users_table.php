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
            $table->boolean('is_ban')->default(0);
            $table->string('email')->unique();
            $table->string('name');
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->string('avatar')->nullable();
            $table->text('about')->nullable();


            /*===== relationships =====*/
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('timeline_id');
            $table->unsignedBigInteger('province_id')->nullable();
//            $table->unsignedBigInteger('country_id')->nullable();
//            $table->unsignedBigInteger('city_id')->nullable();


            /*===== restrict =====*/
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
            $table->foreign('timeline_id')->references('id')->on('timelines')->onDelete('restrict');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('restrict');
//            $table->foreign('country_id')->references('id')->on('countries')->onDelete('restrict');
//            $table->foreign('city_id')->references('id')->on('cities')->onDelete('restrict');

            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
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
