<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_active')->nullable();
            $table->tinyInteger('is_customer')->nullable();
            $table->tinyInteger('is_guest')->nullable();
            $table->string('job_title')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('profile_img')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_img');
            $table->dropColumn('office_phone');
            $table->dropColumn('mobile_phone');
            $table->dropColumn('job_title');
            $table->dropColumn('is_guest');
            $table->dropColumn('is_customer');
            $table->dropColumn('is_active');
        });
    }
}
