<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::transaction(function() {
	        $cnwq_conn = DB::connection('cnwq');
	        $permissions = $cnwq_conn->table('system_data')->where(['list' => 'permission'])->get();
	        $perm = Permission::get()->pluck('id', 'name')->toArray();
	        foreach($permissions as $permission){
	            $m = explode('_', $permission->key);
	            if($m[0] == 'usergroup'){
	                if(is_numeric($m[1])){
	                    $p = unserialize($permission->value);
	                    if(is_array($p)){
	                        foreach($p as $p1){
	                            foreach($p1 as $p2){
	                                foreach($p2 as $pp => $p3){
	                                    if(isset($a) && in_array($pp, $a)){
	                                        continue;
	                                    } else{
	                                        $a[] = $pp;
	                                    }
	                                    if(isset($perm[$pp])){
	                                        $ent = is_array($p3) ? serialize([Institution::class => $p3['shop']]) : null;
	                                        $d[] = ['permission_id' => $perm[$pp], 'role_id' => $m[1], 'entity' => $ent];
	                                    }
	                                }
	                            }
	                        }
	                        unset($a);
	                    }
	                }

	            }
	            
	        }
        	DB::table('role_has_permissions')->insert($d);
        });
    }
}
