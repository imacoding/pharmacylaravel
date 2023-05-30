<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item_code', 100)->nullable();
            $table->string('item_name', 255)->nullable();
            $table->string('group', 255)->nullable();
            $table->string('batch_no', 12)->nullable();
            $table->integer('quantity')->nullable();
            $table->double('cost_price', 10, 2)->nullable();
            $table->double('purchase_price', 10, 2)->nullable();
            $table->string('rack_number', 12)->nullable();
            $table->double('selling_price', 10, 2)->nullable();
            $table->date('expiry')->nullable();
            $table->enum('tax_type', array('PERCENTAGE', 'AMOUNT'))->default('AMOUNT');
            $table->double('tax', 10, 2)->default(0.00);
            $table->text('composition', 65535)->nullable();
            $table->enum('discount_type', array('PERCENTAGE', 'AMOUNT'))->default('AMOUNT');
            $table->double('discount', 10, 2)->deafult(0.00);
            $table->string('manufacturer', 255)->nullable();
            $table->string('marketed_by', 255)->nullable();            
            $table->integer('created_by')->nullable();
            $table->integer('added_by')->nullable();
            $table->boolean('is_delete')->default(0);
            $table->boolean ('is_pres_required' , 2)->default (1);
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
        Schema::dropIfExists('medicine');
    }
}
