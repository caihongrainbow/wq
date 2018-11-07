<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->string('display_name');
            $table->timestamps();
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');

            $table->string('display_name'); 
            $table->integer('ins_id')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->string('img')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('role_levels', function (Blueprint $table){
            $table->integer('ins_id')->nullable();
            $table->integer('level')->nullable();
            $table->integer('apex')->nullable();
            $table->integer('role_id');
            $table->integer('belongs_to');
            $table->unique(['belongs_to', 'ins_id', 'level']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->text('entity')->nullable();
            $table->morphs('model');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->morphs('model');
            $table->integer('belongs_to')->nullable();

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->unique(['role_id', 'model_id', 'model_type', 'belongs_to']);
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->unsignedInteger('permission_id');
            $table->unsignedInteger('role_id');
            $table->text('entity')->nullable();
            $table->primary(['permission_id', 'role_id']);

            app('cache')->forget('spatie.permission.cache');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);

        Schema::dropIfExists('role_levels');
    }
}
