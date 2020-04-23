<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('telephone');
            $table->string('national_code');
            $table->string('national_card_picture');
            $table->string('major');
            $table->enum('certification', ['کارشناسی','کارشناسی ارشد','دکتری']);
            $table->string('city');
            $table->string('resume_pdf');
            $table->string('shaba_number')->nullable();
            $table->text('skills')->nullable();
            $table->unsignedInteger('TOEFL_score')->nullable();
            $table->unsignedInteger('IELTS_score')->nullable();
            $table->unsignedInteger('MSRT_score')->nullable();
            $table->unsignedInteger('GRE_score')->nullable();
            $table->boolean('gender');
            $table->boolean('has_accepted_ruled');
            $table->timestamp('birthday');
            $table->timestamps();
            /*===== relationships =====*/
            $table->unsignedBigInteger('user_id');
            /*===== cascade =====*/
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('translators');
    }
}
