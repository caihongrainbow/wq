<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompletePermissionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_id');
            $table->integer('role_id');
            $table->unique(['ins_id', 'role_id']);
        });
        
        Schema::table('roles', function (Blueprint $table) {
            $table->string('display_name'); 
            $table->tinyInteger('type')->default(0);     
            $table->softDeletes();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('display_name');  
            $table->softDeletes();
        });

        Schema::create('role_has_resources', function (Blueprint $table) {
            $table->increments('resource_id');
            $table->integer('model_id');
            $table->string('model_type', 30);
        });

        Schema::create('model_has_resources', function (Blueprint $table) {
            $table->increments('resource_id');
            $table->integer('model_id');
            $table->string('model_type', 30);
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign('role_has_permissions_role_id_foreign');
            $table->dropForeign('role_has_permissions_permission_id_foreign');
            $table->dropPrimary('role_has_permissions_permission_id_role_id_primary');
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropForeign('model_has_permissions_permission_id_foreign');
            $table->dropPrimary('model_has_permissions_permission_id_model_id_model_type_primary');
        });

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unique(['permission_id', 'role_id']);
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unique(['permission_id', 'model_id', 'model_type']);
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->dropForeign('model_has_roles_role_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institution_roles');
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('display_name');
            $table->dropColumn('type');
            $table->dropSoftDeletes();
        });
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('display_name');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('role_has_resources');
        Schema::dropIfExists('model_has_resources');

        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropColumn('id');

            $table->dropUnique('role_has_permissions_permission_id_role_id_unique');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');


            $table->primary(['permission_id', 'role_id']);
            
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->dropColumn('id');
                
            $table->dropUnique('model_has_permissions_permission_id_model_id_model_type_unique');

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(['permission_id', 'model_id', 'model_type']);
        });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
        });
    }
}
