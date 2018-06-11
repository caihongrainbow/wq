<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompleteInstitutionAndUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_user_credits', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('auth_id');
            $table->integer('ins_id');
            $table->integer('user_id');
            $table->double('history_credit', 15, 2);
            $table->double('current_credit', 15, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['ins_id', 'user_id']);
        });

        Schema::create('institution_staffs', function(Blueprint $table) {
            $table->increments('id');
            $table->string('no')->nullable();
            $table->integer('ins_id');
            $table->integer('user_id');
            $table->integer('auth_id');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['ins_id', 'user_id']);
        });

        Schema::create('institution_admins', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_id');
            $table->integer('user_id');
            $table->unique(['ins_id', 'user_id']);
        });

        Schema::create('institution_user_auths', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('phone_id');
            $table->string('name');
            $table->timestamps();
        });        

        Schema::create('phones', function(Blueprint $table) {
            $table->increments('id');
            $table->string('phone', 20);
            $table->string('area_code', 10);
        });

        Schema::table('users', function(Blueprint $table) {
            $table->string('account', 30)->unique()->nullable();
            $table->dropColumn('phone');
            $table->dropColumn('introduction');
            $table->string('password')->nullable()->change();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institution_user_credits');
        Schema::dropIfExists('institution_staffs');
        Schema::dropIfExists('institution_admins');
        Schema::dropIfExists('institution_roles');
        Schema::dropIfExists('institution_user_auths');
        Schema::dropIfExists('phones');
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('account');
            $table->string('phone')->unique();
            $table->string('introduction');
            $table->string('password')->nullable(false)->change();
            $table->dropSoftDeletes();
        });
    }
}
