<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEdProfessionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ed_professional', function(Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('prof_id')->nullable();
			$table->string('prof_name', 100);
			$table->string('facebook_id', 255)->nullable();
			$table->text('prof_address', 65535)->nullable();
			$table->string('prof_phone', 15)->nullable();
			$table->string('prof_mail', 255)->nullable();
			$table->string('prof_pincode', 8)->nullable();
			// $table->datetime('prof_created_on')->nullable();
			// $table->datetime('prof_updated_on')->nullable();
			$table->boolean('prof_is_delete')->default(0);
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
        Schema::dropIfExists('ed_professional');
    }
}
