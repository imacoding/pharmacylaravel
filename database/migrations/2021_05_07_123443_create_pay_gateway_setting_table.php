<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayGatewaySettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_gateway_setting', function ($table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gateway_id')->nullable();
            $table->foreign('gateway_id')->references('id')->on('payment_gateways')->onDelete('cascade');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('TEXT');
            $table->boolean('is_hidden')->default(0);
            $table->text('dataset')->nullable()->comment('Serialised Data set');
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
        Schema::dropIfExists('pay_gateway_setting');
    }
}
