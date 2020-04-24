<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('needs_tables')->default(0);
            $table->boolean('needs_photos')->default(0);
            $table->boolean('needs_diagrams')->default(0);
            $table->boolean('needs_references')->default(0);
            $table->boolean('needs_subtitles')->default(0);
            $table->boolean('needs_typing_formulas')->default(0);
            $table->boolean('is_secret')->default(0);
            $table->string('original_file')->nullable();
            $table->string('original_file_link')->nullable();
            $table->string('translated_file')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('words')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->timestamp('translator_delivery_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamps();
            /*===== Relationships =====*/
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('operation_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('usage_id');
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('quality_id')->nullable();
            /*===== Cascade =====*/
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            /*===== Restricts =====*/
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('restrict');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('usage_id')->references('id')->on('usages')->onDelete('restrict');
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');
            $table->foreign('quality_id')->references('id')->on('qualities')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
