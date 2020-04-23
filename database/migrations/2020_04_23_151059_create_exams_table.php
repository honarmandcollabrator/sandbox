<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('translated_file');
            $table->enum('status', ['issued', 'translated', 'graded']);
            $table->text('comment');
            $table->timestamp('finished_test_at');
            $table->timestamp('updated_again_at');
            $table->timestamps();
            /*===== Relationships =====*/
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('quality_id');
            $table->unsignedBigInteger('operation_id');
            /*===== Restricts =====*/
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('quality_id')->references('id')->on('qualities')->onDelete('restrict');
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
