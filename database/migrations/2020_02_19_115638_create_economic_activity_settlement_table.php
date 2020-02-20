<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEconomicActivitySettlementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('economic_activity_settlement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('economic_activity_id');
            $table->unsignedBigInteger('settlement_id');
            $table->foreign('economic_activity_id')->references('id')
                ->on('economic_activities');
            $table->foreign('settlement_id')->references('id')
                ->on('settlements');
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
        Schema::dropIfExists('economic_activity_settlement');
    }
}