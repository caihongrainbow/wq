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
            $table->integer('custom_id');
            $table->double('history_credit', 15, 2)->default(0);
            $table->double('current_credit', 15, 2)->default(0);
            $table->primary('custom_id');
            $table->softDeletes();
        });

        Schema::create('institution_user_credits_records', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('custom_id');
            $table->double('credit', 15, 2);
            $table->tinyInteger('in_or_out');
            $table->string('attach')->nullable();
            $table->string('info')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('institution_staffs', function(Blueprint $table) {
            $table->increments('id');
            $table->string('no')->nullable();
            $table->integer('custom_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('institution_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('ins_id');
            $table->string('clientid');
            $table->integer('auth_id')->nullable();
            $table->integer('is_admin')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'ins_id']);
            $table->unique('clientid');
        });

        Schema::create('user_auths', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('phone_id');
            $table->string('name')->nullable();
            $table->integer('user_id');
            $table->dateTime('certified_at')->nullable();
            $table->timestamps();
            $table->unique('phone_id');
        });        

        Schema::create('phones', function(Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number', 20);
            $table->string('area_code', 10);
            $table->unique(['phone_number', 'area_code']);
        });

        Schema::table('users', function(Blueprint $table) {
            $table->string('account', 30)->unique()->nullable();
            $table->string('login_salt', 32)->nullable();
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
        Schema::dropIfExists('institution_user_credits_records');
        Schema::dropIfExists('institution_staffs');
        Schema::dropIfExists('institution_users');
        Schema::dropIfExists('institution_roles');
        Schema::dropIfExists('user_auths');
        Schema::dropIfExists('phones');
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('account');
            $table->dropColumn('login_salt');
            $table->string('phone')->unique();
            $table->string('introduction');
            // $table->string('password')->nullable(false)->change();
            $table->dropSoftDeletes();
        });
    }
}
