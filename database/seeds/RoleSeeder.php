<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            //切换至目标源数据库
            $cnwq_conn = DB::connection('cnwq');
            //获取角色源数据
            $groups = $cnwq_conn->table('user_group')->where(['company_id' => 66, 'is_del' => 0])->get();
            foreach ($groups as $group) {
                $d[] = [
                    'id' => $group->user_group_id, 
                    'name' => (string)$group->user_group_sign, 
                    'guard_name' => "web", 
                    'display_name' => (string)$group->user_group_name, 
                    'ins_id' => $group->row_id,
                    'type' => $group->user_group_type,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
                //该角色拥有等级
                if($group->is_level_role == 1){
                    $l[$group->user_group_id] = $group->user_group_type;
                }
            }
            //插入角色数据
            DB::table('roles')->insert($d);
            //会员等级 id =》 name
            $level_name = [8 => "silver", 9 => "bronze", 10 => "gold", 11 => "platinum", 12 => "diamond"];
            //获取角色等级数据
            $levels = $cnwq_conn->table('user_level_rule')->whereIn('user_group_id', array_keys($l))->get();
            foreach ($levels as $level) {
                if(in_array($level->id, array_keys($level_name))){
                    $item = [
                        'name' => (string)$level_name[$level->id], 
                        'guard_name' => "web", 
                        'display_name' => (string)$level->name, 
                        'ins_id' => $level->company_id,
                        'type' => $l[$level->user_group_id],
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                    //插入为角色数据 原角色等级user_level_rule变为角色roles
                    $id = DB::table('roles')->insertGetId($item);
                    $s = unserialize($level->setting);
                    $dd[] = [
                        'ins_id' => $level->company_id,
                        'level' => $level->level,
                        'apex' => $s[0]['max'], //此值为临界值，超过即升为下一等级
                        'role_id' => $id,
                        'belongs_to' => $level->user_group_id,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                //处理原本角色和等级分开存储导致基本等级（1级）合并后会有2个，例如“会员”角色和“普通用户”等级
                }else{ 
                    if($level->level == 1){ 
                        $dd[] = [
                            'ins_id' => $level->company_id,
                            'level' => $level->level,
                            'apex' => null, //此值为临界值，超过即升为下一等级
                            'role_id' => $level->user_group_id,
                            'belongs_to' => $level->user_group_id,
                            'created_at' => date('Y-m-d H:i:s', time()),
                            'updated_at' => date('Y-m-d H:i:s', time())
                        ];
                    }
                }
            }
            DB::table('role_levels')->insert($dd);
        });
    }
}
