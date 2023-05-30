<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice')->nullable();
            $table->string('description', 100)->nullable();
            $table->string('shipping_address')->nullable();
            $table->unsignedBigInteger('pres_id')->nullable();
            $table->foreign('pres_id')->references('id')->on('prescription')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('status_id')->default(1);
            $table->foreign('status_id')->references('id')->on('invoice_status')->onDelete('cascade');
            $table->double('sub_total', 10, 2)->default(0.00);
            $table->double('discount', 10, 2)->default(0.00);
            $table->float('tax_percentage', 5, 2)->default(0.00);
            $table->double('tax_amount', 10, 2)->default(0.00);
            $table->double('shipping', 10, 2)->default(0.00);
            $table->double('total', 10, 2)->default(0.00);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->unsignedBigInteger('shipping_status')->default(1);
            $table->foreign('shipping_status')->references('id')->on('shipping_status')->onDelete('cascade');
            $table->integer('shipping_mode')->comment('Referencing Shipping Table')->nullable();
            $table->unsignedBigInteger('shipping_id')->comment('Shipping Reference Id')->nullable();
            $table->foreign('shipping_id')->references('id')->on('shipping')->onDelete('cascade');
            $table->unsignedBigInteger('payment_status')->default(1);
            $table->foreign('payment_status')->references('id')->on('payment_status')->onDelete('cascade');
            $table->string('transaction_id')->comment('Payment Reference Transaction ID')->nullable();
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
        Schema::dropIfExists('invoice');
    }
}
