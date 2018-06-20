<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMpTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_mps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appid', 30)->unique();
            $table->string('appsecret', 60);
            $table->timestamps();
        });
        Schema::create('user_wechat_mps', function (Blueprint $table) {
            $table->string('openid', 50);
            $table->string('unionid', 50)->nullable();
            $table->integer('wechat_mp_id');
            $table->integer('user_id');
            $table->primary(['openid', 'user_id']);
            $table->unique(['openid', 'unionid', 'wechat_mp_id']);
        });
        Schema::create('institution_wechat_mps', function (Blueprint $table) {
            $table->integer('ins_id');
            $table->integer('wechat_mp_id');
            $table->tinyInteger('is_on');
            $table->primary(['ins_id', 'wechat_mp_id']);
            $table->unique(['ins_id', 'is_on']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechat_mps');
        Schema::dropIfExists('user_wechat_mps');
        Schema::dropIfExists('institution_wechat_mps');
    }
}
