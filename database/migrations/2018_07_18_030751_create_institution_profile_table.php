<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitutionProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_opens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_id');
            $table->morphs('model');
            $table->tinyInteger('status')->default(1);
        });
        Schema::create('institution_profiles', function (Blueprint $table) {
            $table->string('ins_id');
            $table->integer('field_id');
            $table->string('field_data');
        });
        Schema::create('institution_profile_settings', function (Blueprint $table) {
            $table->increments('field_id');
            $table->integer('type');
            $table->string('field_key');
            $table->string('field_name');
            $table->integer('field_type');
            $table->tinyInteger('visiable');
            $table->tinyInteger('editable');
            $table->tinyInteger('required');
            $table->tinyInteger('privacy');
            $table->string('form_type')->nullable();
            $table->string('form_default_value')->nullable();
            $table->string('validation')->nullable();
            $table->string('tips')->nullable();
            $table->integer('ins_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institution_opens');
        Schema::dropIfExists('institution_profiles');
        Schema::dropIfExists('institution_profile_settings');
    }
}
