<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitutionAndTypeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //机构
        Schema::create('institutions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('orgid', 50)->unique()->nullable();
            $table->integer('parent_id')->default(0);
            $table->integer('type_id');
            $table->tinyInteger('is_init')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('institution_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 30);
            $table->string('sign', 30);
            $table->softDeletes();
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
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('institution_types');
    }
}
