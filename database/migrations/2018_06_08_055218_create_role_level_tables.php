<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleLevelTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('rules', 50);
            $table->integer('level');
            $table->integer('role_id');
            $table->integer('ins_id');
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('user_role_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('role_level_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_levels');
        Schema::dropIfExists('user_role_levels');
    }
}
