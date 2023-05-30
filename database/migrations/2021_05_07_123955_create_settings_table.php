<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function ($table) {
            $table->bigIncrements('id');
            $table->string('group')->nullable();
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->enum('type', array("TEXT", "IMAGE", "INTEGER", "FLOAT", "SERIALIZED", "HASHED"))->default("TEXT");
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('settings');
    }
}
