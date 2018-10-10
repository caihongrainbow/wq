<?php

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Role;
use App\Models\InstitutionType;

class DataInitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 创建超级管理员账号
        $salt = rand(10000, 99999);
        $user = User::create([
            'name' => '超级管理员',
            'account' => 'super',
            'login_salt' => $salt,
            'password' => md5PlusSalt('superadmin', $salt),
        ]);
        $roleSuper = Role::create([
            'name' => 'super',
            'display_name' => '超级管理员',
        ]);
        $user->assignRole('super');

        // 创建系统角色
        $roleAdmin = Role::create([
            'name' => 'admin',
            'display_name' => '管理员',
        ]);
        $roleUser = Role::create([
            'name' => 'user',
            'display_name' => '用户',
        ]);

        InstitutionType::create(['title' => '集团', 'sign' => 'group']);
        InstitutionType::create(['title' => '公司', 'sign' => 'company']);
        InstitutionType::create(['title' => '部门', 'sign' => 'department']);
        InstitutionType::create(['title' => '酒店', 'sign' => 'hotel']);
        InstitutionType::create(['title' => '餐饮', 'sign' => 'catering']);
        InstitutionType::create(['title' => '温泉', 'sign' => 'hotwell']);
        InstitutionType::create(['title' => '办事处', 'sign' => 'office']);
        InstitutionType::create(['title' => '旅行社', 'sign' => 'travel_agency']);
        InstitutionType::create(['title' => '返租', 'sign' => 'leaseback']);
        InstitutionType::create(['title' => '保健按摩', 'sign' => 'massage']);
    }
}
