<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 255)->nullable();
            $table->string('password', 64)->nullable();
            $table->string('phone', 15)->nullable();
            $table->unsignedBigInteger('user_type_id');
            $table->foreign('user_type_id')->references('id')->on('user_type')->onDelete('cascade');
            $table->string('security_code', 20)->nullable();
            $table->unsignedBigInteger('user_status')->default(1);
            $table->foreign('user_status')->references('id')->on('user_status')->onDelete('cascade');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('user_id')->nullable()->comment("References Customer,Medical Professional, Admin Table based on user type");
            $table->string('remember_token', 64)->nullable();
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
        Schema::dropIfExists('users');
    }
}
