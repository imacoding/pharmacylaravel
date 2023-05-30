<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('invoice')->onDelete('cascade');
            $table->unsignedBigInteger('medicine')->nullable();
            $table->foreign('medicine')->references('id')->on('medicine')->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->double('unit_price', 10, 2)->default(0.00);
            $table->double('total_price', 10, 2)->default(0.00);
            $table->string('discount_type')->nullable();
            $table->double('discount_percentage', 10, 2)->default(0.00);
            $table->float('unit_discount', 5, 2)->nullable();
            $table->double('discount', 10, 2)->default(0.00);
            $table->boolean('is_removed')->default(0);            
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('cart');
    }
}
