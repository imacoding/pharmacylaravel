<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_pic',255)->nullable()->after('user_id');
            $table->string('country_code',5)->nullable()->after('password');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'profile_pic'))
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('profile_pic');
                $table->dropColumn('country_code');
            });
        }
    }
}
