<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->timestamps();
            /*===== Relationships =====*/
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('support_id');
            $table->unsignedBigInteger('ticket_status_id');
            /*===== Restrict =====*/
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('support_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
