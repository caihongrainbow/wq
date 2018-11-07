<?php

use Illuminate\Database\Seeder;

use App\Models\InstitutionProfileSetting;

class InstitutionProfileSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //切换至目标源数据库
        // $cnwq_conn = DB::connection('cnwq');
        // $settings = $cnwq_conn->table('institution_profile_setting')->get();
        // foreach ($settings as $setting) {
        //     $d[] = [
        //         'field_id' => $setting->field_id, 
        //         'type' => $setting->type, 
        //         'field_key' => $setting->field_key, 
        //         'field_name' => $setting->field_name, 
        //         'field_type' => $setting->field_type,
        //         'visiable' => $setting->visiable,
        //         'editable' => $setting->editable,
        //         'required' => $setting->required,
        //         'privacy' => $setting->privacy
        //     ];
        // }
        // DB::table('institution_profile_settings')->insert($d);
    }
}
