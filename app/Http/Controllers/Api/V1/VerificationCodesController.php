<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Handlers\QcloudSmsHandler;
use App\Http\Requests\Api\VerificationCodeRequest;



class VerificationCodesController extends Controller
{

    protected $appid = 1400043668;
    protected $appkey = "192df2867d21c087116a64068659c2ab";
    protected $smsSign = "御温泉";

    public function store(VerificationCodeRequest $request)
    {
        date_default_timezone_set("Asia/Shanghai");
        $phone = $request->phone_number;
        $areaCode = $request->area_code;

        $templateId = 137359;

        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
        $minutes = 10;
        //暂定
        $params = [$code, $minutes];

        $qcloud = new QcloudSmsHandler();
        $return = $qcloud->sendSignleSmsByTemplate($areaCode, $phone, $templateId, $params);

        if($return['result'] == 0){
            $key = 'verificationCode_'.$phone;
            $expiredAt = now()->addMinutes($minutes);
            // 缓存验证码 10分钟过期。
            \Cache::put($key, $code, $expiredAt);

            return $this->response->created();
        }else{
            return $this->response->errorInternal($return['errmsg'] ?? '短信发送异常');
        }

    
    }
}
