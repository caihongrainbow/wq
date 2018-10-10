<?php

use Illuminate\Database\Seeder;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //切换至目标源数据库
        $cnwq_conn = DB::connection('cnwq');
        //获取即将处理的数据，包括公司、店数据
        $companys = $cnwq_conn->table('company')->where('cid', 66)->get();
        $shops = $cnwq_conn->table('shop')->where(['company_id' => 66, 'is_del' => 0])->get();

        DB::transaction(function() use ($companys, $shops) {
            //原店分类ID =》新机构分类 
            $types = $shops->pluck('shop_category_id')->unique();
            foreach($types as $type){
                switch ($type) {
                    case 29:
                        $t[29] = 3;
                        break;
                    case 30:
                        $t[30] = 4;
                        break;
                    case 33:
                        $t[33] = 7;
                        break;
                    case 34:
                        $t[34] = 5;
                        break;
                    case 35:
                        $t[35] = 6;
                        break;
                    case 36:
                        $t[36] = 8;
                        break;
                    case 50:
                        $t[50] = 9;
                        break;
                    case 51:
                        $t[51] = 10;
                        break;
                }
            }
            //机构数据处理
            foreach($companys as $company){
                $d[] = [
                    'id' => $company->cid, 
                    'name' => $company->cname, 
                    'parent_id' => 0,
                    'type_id' => 2,
                    'is_init' => 1,
                    'created_at' => date('Y-m-d H:i:s', $company->ctime),
                    'updated_at' => date('Y-m-d H:i:s', $company->rtime)
                ];
            }
            foreach ($shops as $shop) {
                $d[] = [
                    'id' => $shop->sid, 
                    'name' => $shop->sname, 
                    'parent_id' => 66,
                    'type_id' => $t[$shop->shop_category_id],
                    'is_init' => 1,
                    'created_at' => date('Y-m-d H:i:s', $shop->ctime),
                    'updated_at' => date('Y-m-d H:i:s', $shop->rtime)
                ];
            }
            //插入机构数据
            DB::table('institutions')->insert($d);
        });
    }
}
