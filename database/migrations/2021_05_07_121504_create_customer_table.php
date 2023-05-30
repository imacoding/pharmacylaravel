<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('facebook_id', 255)->nullable();
            $table->text('address', 65535)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('mail', 255)->nullable();
            $table->string('pincode', 8)->nullable();
            $table->integer('country')->comment('Referencing Country Table')->nullable();            
            $table->boolean('is_delete')->default(0);
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
        Schema::dropIfExists('customer');
    }
}
