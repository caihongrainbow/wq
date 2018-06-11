<?php

namespace App\Handlers;

use Qcloud\Sms\SmsSingleSender;

class QcloudSmsHandler
{
	protected $appid;
	protected $appkey;
	protected $smsSign;

	public function __construct($appid = 1400043668, $appkey = '192df2867d21c087116a64068659c2ab', $smsSign = '御温泉'){
		$this->appid = $appid;
		$this->appkey = $appkey;
		$this->smsSign = $smsSign;
	}

	/**
	 * [sendSignleSmsByTemplate 按照ID单发一条短信]
	 * @Author   CaiHong
	 * @DateTime 2018-06-10
	 * @param    [type]     $areaCode    [description]
	 * @param    [type]     $phoneNumber [description]
	 * @param    [type]     $templateId  [description]
	 * @param    [type]     $params      [description]
	 * @return   [type]                  [description]
	 */
	public function sendSignleSmsByTemplate($areaCode, $phoneNumber, $templateId, $params){
		try {
		    $ssender = new SmsSingleSender($this->appid, $this->appkey);
		    $result = $ssender->sendWithParam($areaCode, $phoneNumber, $templateId,
		        $params, $this->smsSign, "", "");  
		    return json_decode($result, true);
		} catch(\Exception $exception) {
            echo var_dump($e);
		}
		
	}

}