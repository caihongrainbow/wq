<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
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
            $perm = [];
            foreach($permissions as $permission){
                $p = unserialize($permission->value);
                if(is_array($p)){
                    foreach($p as $p1){
                        foreach($p1 as $p2){
                            foreach($p2 as $pp => $p3){
                                !in_array($pp, $perm) && array_push($perm, $pp);
                            }
                        }
                    }
                }
            }
            $nodes = $cnwq_conn->table('permission_node')->whereIn('rule', $perm)->get()->pluck('ruleinfo', 'rule');
            foreach($nodes as $k => $v){
                $n[] = ['name' => $k, 'guard_name' => 'web', 'display_name' => $v];
            }
            DB::table('permissions')->insert($n);
        });
    }
}
