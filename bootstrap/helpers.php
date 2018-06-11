<?php 

/**
 * [isLogin 是否登录]
 * @Author   CaiHong
 * @DateTime 2018-06-08
 * @return   boolean    [description]
 */
function isLogin(){
	$sessionKey = config('session.login_key');
	
	$session = session($sessionKey, []);

	return $session ? true : false;
}

/**
 * [makeOrgid 制作orgid]
 * @Author   CaiHong
 * @DateTime 2018-06-08
 * @return   [type]     [description]
 */
function makeOrgid($key){
	return 'wq'.substr(md5($key),8,16);
}


function phoneValidate($areaCode, $phonenumber){
	switch ($areaCode) {
		case '+852': //香港	 
			$validate = "/^([6|9])\d{7}$/";
			break;
		case '+853': //澳门 
			$validate = "/^[6]([8|6])\d{6}$/";
			break;
		case '+886': //香港 
			$validate = "/^[0][9]\d{8}$/";
			break;												
		default:
			$validate = "/^[1][3-8]\d{9}$/";
			break;
	}

	return preg_match($validate, $phonenumber);
}